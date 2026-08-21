#!/usr/bin/env bash
set -euo pipefail

# deploy-cloudpanel.sh
# Deploy do Academy (CodeIgniter 4) para VPS Hostinger com CloudPanel.
#
# Site:
# - Dominio: academy.mechanical.co.mz
# - Caminho remoto CloudPanel: /home/mechanical-academy/htdocs/academy.mechanical.co.mz
# - DocumentRoot no Nginx/CloudPanel deve apontar para:
#   /home/mechanical-academy/htdocs/academy.mechanical.co.mz/public
#
# Fluxo:
# 1) Commit + push para o GitHub (mantido)
# 2) Build local de assets (npm), se existir
# 3) Sync via rsync/tar para o servidor
# 4) composer install + migrations + cache clear no servidor
#
# Protecoes:
# - Nao envia .env por padrao (use ENV_FILE=... para enviar).
# - Nao apaga/sobrescreve writable/uploads, logs, cache, session e debugbar.
# - Nao envia vendor, node_modules, logs, cache, sessoes e ficheiros de teste.
#
# Uso:
#   ./deploy-cloudpanel.sh
#   DEPLOY_HOST=168.231.113.60 DEPLOY_USER=mechanical-academy ./deploy-cloudpanel.sh
#   ENV_FILE=.env.production ./deploy-cloudpanel.sh
#   DRY_RUN=yes ./deploy-cloudpanel.sh
#   SKIP_GIT=yes ./deploy-cloudpanel.sh
#   SSH_OPTS="-i ~/.ssh/id_rsa" ./deploy-cloudpanel.sh
#   COMMIT_MSG="fix: demo enroll" ./deploy-cloudpanel.sh

SOURCE_DIR="${SOURCE_DIR:-.}"

REMOTE_USER="${DEPLOY_USER:-mechanical-academy}"
REMOTE_HOST="${DEPLOY_HOST:-168.231.113.60}"
REMOTE_PATH="${DEPLOY_PATH:-/home/mechanical-academy/htdocs/academy.mechanical.co.mz}"
REMOTE_PORT="${DEPLOY_PORT:-22}"
SITE_OWNER="${SITE_OWNER:-mechanical-academy}"
PUBLIC_URL="${PUBLIC_URL:-https://academy.mechanical.co.mz}"

SSH_OPTS="${SSH_OPTS:-}"
SSH_IDENTITY_FILE="${SSH_IDENTITY_FILE:-}"

REMOTE_NAME="${REMOTE_NAME:-origin}"
BRANCH_NAME="${BRANCH_NAME:-main}"
COMMIT_MSG="${COMMIT_MSG:-}"
SKIP_GIT="${SKIP_GIT:-no}"
SKIP_NPM="${SKIP_NPM:-no}"

ENV_FILE="${ENV_FILE:-}"

RUN_COMPOSER="${RUN_COMPOSER:-yes}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-yes}"
RESTART_WEBSERVER="${RESTART_WEBSERVER:-yes}"
DRY_RUN="${DRY_RUN:-no}"

COMPOSER_BIN="${COMPOSER_BIN:-composer}"
PHP_BIN="${PHP_BIN:-php}"

# Reuse the same SSH connection so the password is requested only once.
# On Git Bash (MINGW/MSYS) multiplexing is unreliable, so disable by default.
SSH_MULTIPLEX="${SSH_MULTIPLEX:-auto}"
if [[ "${SSH_MULTIPLEX}" == "auto" ]]; then
  case "${MSYSTEM:-}" in
    MINGW*|MSYS*) SSH_MULTIPLEX="off" ;;
    *) SSH_MULTIPLEX="on" ;;
  esac
fi

SSH_BASE_OPTS=()

if [[ -n "${SSH_IDENTITY_FILE}" ]]; then
  SSH_BASE_OPTS+=(-i "${SSH_IDENTITY_FILE}")
fi

if [[ -n "${SSH_OPTS}" ]]; then
  # shellcheck disable=SC2206
  read -r -a SSH_EXTRA_OPTS <<< "${SSH_OPTS}"
  SSH_BASE_OPTS+=("${SSH_EXTRA_OPTS[@]}")
fi

if [[ "${SSH_MULTIPLEX}" == "on" ]]; then
  SAFE_HOST="${REMOTE_HOST//[:]/_}"
  CONTROL_PATH="${CONTROL_PATH:-/tmp/ssh-${REMOTE_USER}@${SAFE_HOST}-${REMOTE_PORT}}"
  SSH_BASE_OPTS+=(-o ControlMaster=auto -o ControlPersist=10m -o "ControlPath=${CONTROL_PATH}")
fi

SSH_CMD=(ssh -p "${REMOTE_PORT}" "${SSH_BASE_OPTS[@]}")
SCP_CMD=(scp -P "${REMOTE_PORT}" "${SSH_BASE_OPTS[@]}")

RSYNC_SSH_CMD=(ssh -p "${REMOTE_PORT}")
if [[ -n "${SSH_IDENTITY_FILE}" ]]; then
  RSYNC_SSH_CMD+=(-i "${SSH_IDENTITY_FILE}")
fi
if [[ -n "${SSH_OPTS}" ]]; then
  # shellcheck disable=SC2206
  read -r -a RSYNC_SSH_EXTRA <<< "${SSH_OPTS}"
  RSYNC_SSH_CMD+=("${RSYNC_SSH_EXTRA[@]}")
fi
if [[ "${SSH_MULTIPLEX}" == "on" ]]; then
  RSYNC_SSH_CMD+=(-o ControlMaster=auto -o ControlPersist=10m -o "ControlPath=${CONTROL_PATH}")
fi

RSYNC_SSH=$(printf '%q ' "${RSYNC_SSH_CMD[@]}")
RSYNC_SSH=${RSYNC_SSH% }

log() {
  echo ">>> $*"
}

run() {
  if [[ "${DRY_RUN}" == "yes" ]]; then
    log "[dry-run] $*"
    return 0
  fi

  "$@"
}

remote_exec() {
  "${SSH_CMD[@]}" "${REMOTE_USER}@${REMOTE_HOST}" "$@"
}

remote_exec_mutating() {
  if [[ "${DRY_RUN}" == "yes" ]]; then
    log "[dry-run] ssh ${REMOTE_USER}@${REMOTE_HOST} $*"
    return 0
  fi

  remote_exec "$@"
}

require_command() {
  if ! command -v "$1" >/dev/null 2>&1; then
    echo "Erro: comando '$1' nao encontrado no PATH."
    exit 1
  fi
}

if [[ -z "${REMOTE_USER}" || -z "${REMOTE_HOST}" || -z "${REMOTE_PATH}" ]]; then
  echo "Erro: defina DEPLOY_USER, DEPLOY_HOST e DEPLOY_PATH antes de executar o deploy."
  exit 1
fi

if [[ ! -f "${SOURCE_DIR}/spark" ]]; then
  echo "Erro: este script deve ser executado na raiz do projeto CodeIgniter 4."
  echo "Arquivo spark nao encontrado em: ${SOURCE_DIR}"
  exit 1
fi

if [[ ! -f "${SOURCE_DIR}/composer.json" ]]; then
  echo "Erro: composer.json nao encontrado."
  echo "Verifique se SOURCE_DIR aponta para a pasta do projeto."
  exit 1
fi

require_command ssh
require_command scp

log "Deploy Academy (CodeIgniter 4) para CloudPanel"
log "Origem: ${SOURCE_DIR}"
log "Destino: ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}"
log "Site: ${PUBLIC_URL}"

if [[ "${DRY_RUN}" == "yes" ]]; then
  log "Modo dry-run ativo: nenhuma alteracao sera aplicada no servidor (git local ainda pode correr)."
fi

# -----------------------------------------------------------------------------
# 1) GitHub: commit + push (mantido)
# -----------------------------------------------------------------------------
if [[ "${SKIP_GIT}" == "yes" ]]; then
  log "GitHub ignorado (SKIP_GIT=yes)."
else
  log "Commitando e enviando para o GitHub (branch ${BRANCH_NAME})"
  if git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
    if [[ -n "$(git status --porcelain --ignore-submodules=dirty)" ]]; then
      if [[ -z "${COMMIT_MSG}" ]]; then
        COMMIT_MSG="deploy: $(date +'%Y-%m-%d %H:%M:%S')"
      fi

      if [[ "${DRY_RUN}" == "yes" ]]; then
        log "[dry-run] git add -A && git commit -m '${COMMIT_MSG}' && git push ${REMOTE_NAME} ${BRANCH_NAME}"
      else
        git add -A
        git reset HEAD -- .env .env.* >/dev/null 2>&1 || true
        if git diff --cached --quiet; then
          log "Sem alteracoes para commit. Pulando commit/push."
        else
          git commit -m "${COMMIT_MSG}"
          git push "${REMOTE_NAME}" "${BRANCH_NAME}"
          log "Push para ${REMOTE_NAME}/${BRANCH_NAME} concluido."
        fi
      fi
    else
      log "Sem alteracoes locais. Pulando commit/push."
    fi
  else
    log "Aviso: repositorio git nao encontrado. Pulando commit/push."
  fi
fi

# -----------------------------------------------------------------------------
# 2) Build local de assets
# -----------------------------------------------------------------------------
if [[ "${SKIP_NPM}" == "yes" ]]; then
  log "Build npm ignorado (SKIP_NPM=yes)."
elif [[ -f "${SOURCE_DIR}/package.json" ]]; then
  log "Gerando assets (npm)"
  if command -v npm >/dev/null 2>&1; then
    if [[ "${DRY_RUN}" == "yes" ]]; then
      log "[dry-run] npm install && npm run build (se existir)"
    else
      (
        cd "${SOURCE_DIR}"
        npm install
        if npm run | grep -qE '(^| )build'; then
          npm run build
        else
          log "Script npm 'build' nao encontrado. Continuando."
        fi
      )
    fi
  else
    log "Aviso: npm nao encontrado. Pulando build."
  fi
else
  log "package.json nao encontrado. Pulando build npm."
fi

# -----------------------------------------------------------------------------
# 3) SSH + estrutura remota
# -----------------------------------------------------------------------------
log "Testando conexao SSH"
remote_exec "echo 'SSH OK'"

log "Criando estrutura remota"
remote_exec_mutating "
  mkdir -p '${REMOTE_PATH}'
  mkdir -p '${REMOTE_PATH}/public'
  mkdir -p '${REMOTE_PATH}/writable/uploads'
  mkdir -p '${REMOTE_PATH}/writable/logs'
  mkdir -p '${REMOTE_PATH}/writable/cache'
  mkdir -p '${REMOTE_PATH}/writable/session'
  mkdir -p '${REMOTE_PATH}/writable/debugbar'
"

RSYNC_EXCLUDES=(
  --exclude '.git'
  --exclude '.env'
  --exclude '.env.*'
  --exclude 'vendor'
  --exclude 'writable/uploads'
  --exclude 'writable/logs'
  --exclude 'writable/cache'
  --exclude 'writable/session'
  --exclude 'writable/debugbar'
  --exclude 'tests'
  --exclude '.phpunit.result.cache'
  --exclude 'node_modules'
  --exclude '.cursor'
  --exclude '.vscode'
  --exclude '.idea'
  --exclude 'Thumbs.db'
  --exclude '.DS_Store'
  --exclude 'public/assets/img/_orig'
  --exclude 'deploy.ps1'
  --exclude '*.tar.gz'
)

RSYNC_PROTECT=(
  --filter 'protect writable/uploads/'
  --filter 'protect writable/logs/'
  --filter 'protect writable/cache/'
  --filter 'protect writable/session/'
  --filter 'protect writable/debugbar/'
  --filter 'protect .env'
)

log "Sincronizando arquivos"

if command -v rsync >/dev/null 2>&1; then
  log "rsync encontrado. Usando rsync."

  RSYNC_ARGS=(
    -avz
    --delete
    "${RSYNC_PROTECT[@]}"
    "${RSYNC_EXCLUDES[@]}"
    -e "${RSYNC_SSH}"
    "${SOURCE_DIR}/"
    "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/"
  )

  if [[ "${DRY_RUN}" == "yes" ]]; then
    RSYNC_ARGS=(--dry-run "${RSYNC_ARGS[@]}")
  fi

  rsync "${RSYNC_ARGS[@]}"
else
  log "rsync nao encontrado. Usando pacote tar.gz + scp."
  log "Aviso: este modo nao apaga arquivos antigos no servidor."

  PACKAGE_NAME="academy-deploy-$(date +%Y%m%d%H%M%S).tar.gz"

  if [[ -n "${TMPDIR:-}" ]]; then
    PACKAGE_PATH="${TMPDIR}/${PACKAGE_NAME}"
  elif [[ -d /tmp ]]; then
    PACKAGE_PATH="/tmp/${PACKAGE_NAME}"
  else
    PACKAGE_PATH="./${PACKAGE_NAME}"
  fi

  log "Gerando pacote local: ${PACKAGE_PATH}"

  TAR_EXCLUDES=(
    --exclude='.git'
    --exclude='.env'
    --exclude='.env.*'
    --exclude='vendor'
    --exclude='writable/uploads'
    --exclude='writable/logs'
    --exclude='writable/cache'
    --exclude='writable/session'
    --exclude='writable/debugbar'
    --exclude='tests'
    --exclude='.phpunit.result.cache'
    --exclude='node_modules'
    --exclude='.cursor'
    --exclude='.vscode'
    --exclude='.idea'
    --exclude='public/assets/img/_orig'
    --exclude='*.tar.gz'
  )

  run tar "${TAR_EXCLUDES[@]}" -czf "${PACKAGE_PATH}" -C "${SOURCE_DIR}" .

  log "Enviando pacote para o servidor"
  run "${SCP_CMD[@]}" "${PACKAGE_PATH}" "${REMOTE_USER}@${REMOTE_HOST}:/tmp/${PACKAGE_NAME}"

  log "Extraindo pacote no servidor"
  remote_exec_mutating "
    mkdir -p '${REMOTE_PATH}' &&
    tar -xzf '/tmp/${PACKAGE_NAME}' -C '${REMOTE_PATH}' &&
    rm -f '/tmp/${PACKAGE_NAME}'
  "

  log "Removendo pacote local"
  run rm -f "${PACKAGE_PATH}"
fi

# -----------------------------------------------------------------------------
# 4) .env (opcional)
# -----------------------------------------------------------------------------
if [[ -n "${ENV_FILE}" ]]; then
  if [[ -f "${ENV_FILE}" ]]; then
    log "Enviando ENV_FILE como .env de producao"
    run "${SCP_CMD[@]}" "${ENV_FILE}" "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/.env"
  else
    echo "Erro: ENV_FILE='${ENV_FILE}' nao encontrado."
    exit 1
  fi
else
  log ".env nao enviado. Mantendo .env existente no servidor."
fi

# Envia só as chaves Google (o .env completo continua fora do Git/rsync).
# Isolado com set +e: grep sem match / SSH falho nao podem abortar o deploy.
SYNC_GOOGLE_ENV="${SYNC_GOOGLE_ENV:-yes}"
read_dotenv_value() {
  local key="$1"
  local file="$2"
  local line=""
  [[ -f "${file}" ]] || return 0
  line="$(grep -E "^[[:space:]]*${key}[[:space:]]*=" "${file}" 2>/dev/null | tail -1 || true)"
  [[ -n "${line}" ]] || return 0
  printf '%s' "${line}" | sed -E 's/^[^=]+=//; s/^[[:space:]]+//; s/[[:space:]]+$//; s/^["'\'']//; s/["'\'']$//'
  return 0
}

if [[ "${SYNC_GOOGLE_ENV}" == "yes" && -f "${SOURCE_DIR}/.env" ]]; then
  set +e
  GOOGLE_ID_SYNC="$(read_dotenv_value 'google.clientId' "${SOURCE_DIR}/.env")"
  GOOGLE_SECRET_SYNC="$(read_dotenv_value 'google.clientSecret' "${SOURCE_DIR}/.env")"
  GOOGLE_REDIRECT_SYNC="$(read_dotenv_value 'google.redirectUri' "${SOURCE_DIR}/.env")"

  if [[ -n "${GOOGLE_ID_SYNC}" && -n "${GOOGLE_SECRET_SYNC}" ]]; then
    log "A gravar chaves Google no .env remoto (sem enviar o ficheiro .env)"

    GOOGLE_PAYLOAD_B64="$(
      GOOGLE_ID_SYNC="${GOOGLE_ID_SYNC}" \
      GOOGLE_SECRET_SYNC="${GOOGLE_SECRET_SYNC}" \
      GOOGLE_REDIRECT_SYNC="${GOOGLE_REDIRECT_SYNC}" \
      php -r 'echo base64_encode(json_encode(array_filter([
        "google.clientId" => getenv("GOOGLE_ID_SYNC") ?: "",
        "google.clientSecret" => getenv("GOOGLE_SECRET_SYNC") ?: "",
        "google.redirectUri" => getenv("GOOGLE_REDIRECT_SYNC") ?: "",
      ], static function ($v) { return $v !== ""; })));' 2>/dev/null
    )"

    if [[ -z "${GOOGLE_PAYLOAD_B64}" ]]; then
      log "Aviso: nao foi possivel preparar payload Google; a continuar o deploy."
    else
      remote_exec_mutating "
cd '${REMOTE_PATH}'
touch .env
if [ -f scripts/upsert_env.php ]; then
  '${PHP_BIN}' scripts/upsert_env.php .env '${GOOGLE_PAYLOAD_B64}'
else
  echo 'Aviso: scripts/upsert_env.php em falta; sync Google ignorado.'
fi
"
      if [[ $? -ne 0 ]]; then
        log "Aviso: sync Google no servidor falhou; a continuar o deploy."
      fi
    fi
  else
    log "Aviso: google.clientId/google.clientSecret ausentes no .env local; botao Google no servidor pode falhar."
  fi
  set -e
fi

# -----------------------------------------------------------------------------
# 5–9) Pos-deploy remoto (uma unica sessao SSH = uma password no Git Bash)
# -----------------------------------------------------------------------------
log "Pos-deploy no servidor (permissoes, composer, migrations, cache)"

REMOTE_POST_DEPLOY="
set -e
cd '${REMOTE_PATH}'

# --- permissoes (CloudPanel: nao fazer chown -R na raiz; sessoes/cache sao do www-data) ---
mkdir -p writable/uploads writable/logs writable/cache writable/session writable/debugbar
chmod -R ug+rwX writable 2>/dev/null || chmod -R 775 writable 2>/dev/null || true

fix_writable_ownership() {
  local target=\"\$1\"
  if chown -R '${SITE_OWNER}:www-data' \"\${target}\" 2>/dev/null; then
    return 0
  fi
  if chown -R '${SITE_OWNER}:${SITE_OWNER}' \"\${target}\" 2>/dev/null; then
    return 0
  fi
  return 1
}

if id 'www-data' >/dev/null 2>&1; then
  fix_writable_ownership writable/uploads || true
  fix_writable_ownership writable/logs || true
  fix_writable_ownership writable/cache || true
  fix_writable_ownership writable/session || true
  fix_writable_ownership writable/debugbar || true
fi
"

if [[ "${RUN_COMPOSER}" == "yes" ]]; then
  REMOTE_POST_DEPLOY+="
echo '>>> composer install'
if ! command -v '${COMPOSER_BIN}' >/dev/null 2>&1; then
  echo 'Erro: ${COMPOSER_BIN} nao encontrado no servidor.'
  exit 1
fi
COMPOSER_ALLOW_SUPERUSER=1 '${COMPOSER_BIN}' install --no-dev --optimize-autoloader --prefer-dist --no-interaction
"
fi

if [[ "${RUN_MIGRATIONS}" == "yes" ]]; then
  REMOTE_POST_DEPLOY+="
echo '>>> migrations'
if ! command -v '${PHP_BIN}' >/dev/null 2>&1; then
  echo 'Erro: ${PHP_BIN} nao encontrado no servidor.'
  exit 1
fi
'${PHP_BIN}' spark migrate --all
"
fi

REMOTE_POST_DEPLOY+="
echo '>>> cache clear'
'${PHP_BIN}' spark cache:clear || true
"

if [[ "${RESTART_WEBSERVER}" == "yes" ]]; then
  REMOTE_POST_DEPLOY+="
echo '>>> nginx reload (opcional)'
if command -v sudo >/dev/null 2>&1 && sudo -n nginx -t >/dev/null 2>&1; then
  sudo nginx -t && sudo systemctl reload nginx 2>/dev/null || sudo service nginx reload 2>/dev/null || true
elif command -v nginx >/dev/null 2>&1 && nginx -t >/dev/null 2>&1; then
  systemctl reload nginx 2>/dev/null || service nginx reload 2>/dev/null || true
else
  echo 'Aviso: recarga Nginx ignorada (CloudPanel gere o Nginx; nao e necessario no deploy PHP).'
fi
"
fi

remote_exec_mutating "${REMOTE_POST_DEPLOY}"

if [[ "${RUN_COMPOSER}" != "yes" ]]; then
  log "Composer ignorado (RUN_COMPOSER=${RUN_COMPOSER})."
fi
if [[ "${RUN_MIGRATIONS}" != "yes" ]]; then
  log "Migrations ignoradas (RUN_MIGRATIONS=${RUN_MIGRATIONS})."
fi
if [[ "${RESTART_WEBSERVER}" != "yes" ]]; then
  log "Recarga do servidor web ignorada (RESTART_WEBSERVER=${RESTART_WEBSERVER})."
fi

log "Deploy concluido."
log "Site: ${PUBLIC_URL}"
log "Caminho remoto: ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}"
