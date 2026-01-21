#!/usr/bin/env bash
set -euo pipefail

# deploy-cloudpanel.sh
# Sincroniza o projeto via SSH/rsync e executa tarefas no servidor.

SOURCE_DIR="${SOURCE_DIR:-.}"
REMOTE_USER="${DEPLOY_USER:-mechanical-academy}"
REMOTE_HOST="${DEPLOY_HOST:-168.231.113.60}"
REMOTE_PATH="${DEPLOY_PATH:-/home/mechanical-academy/htdocs/academy.mechanical.co.mz}"
LOCAL_ENV_FILE="${LOCAL_ENV_FILE:-.env}"
REMOTE_NAME="${REMOTE_NAME:-origin}"
BRANCH_NAME="${BRANCH_NAME:-main}"
REMOTE_PORT="${DEPLOY_PORT:-22}"
SSH_OPTS="${SSH_OPTS:-}"
TAR_OPTS="${TAR_OPTS:---exclude=.git --exclude=node_modules --exclude=build/logs --exclude=vendor --exclude=.env}"

# Reuse the same SSH connection so the password is requested only once.
SAFE_HOST="${REMOTE_HOST//[:]/_}"
CONTROL_PATH="${CONTROL_PATH:-$HOME/.ssh/cm-${REMOTE_USER}@${SAFE_HOST}-${REMOTE_PORT}}"
SSH_OPTS="${SSH_OPTS} -o ControlMaster=auto -o ControlPersist=10m -o ControlPath=${CONTROL_PATH}"

if [[ -z "${REMOTE_USER}" || -z "${REMOTE_HOST}" || -z "${REMOTE_PATH}" ]]; then
  echo "Defina DEPLOY_USER, DEPLOY_HOST e DEPLOY_PATH antes de executar o deploy."
  exit 1
fi

echo ">>> Commitando e enviando para o GitHub (branch ${BRANCH_NAME})"
if git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  if [[ -n "$(git status --porcelain)" ]]; then
    COMMIT_MSG="deploy: $(date +'%Y-%m-%d %H:%M:%S')"
    git add -A
    git commit -m "${COMMIT_MSG}"
    git push "${REMOTE_NAME}" "${BRANCH_NAME}"
  else
    echo ">>> Sem alteracoes locais. Pulando commit/push."
  fi
else
  echo ">>> Aviso: repositorio git nao encontrado. Pulando commit/push."
fi

echo ">>> Gerando assets (npm run build se houver)"
if [[ -f package.json ]]; then
  if command -v npm >/dev/null 2>&1; then
    npm install

    if npm run | grep -q "build"; then
      npm run build
    fi
  else
    echo ">>> Aviso: npm nao encontrado. Pulando build."
  fi
fi

echo ">>> Sincronizando arquivos para ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}"
if command -v rsync >/dev/null 2>&1; then
  rsync -avz --delete \
    --exclude '.git' \
    --exclude 'node_modules' \
    --exclude 'build/logs' \
    --exclude 'vendor' \
    --exclude '.env' \
    -e "ssh ${SSH_OPTS} -p ${REMOTE_PORT}" \
    "${SOURCE_DIR}/" "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}"
else
  echo ">>> rsync nao encontrado. Usando tar+ssh (sem delete remoto)."
  tar -czf - ${TAR_OPTS} "${SOURCE_DIR}" | ssh ${SSH_OPTS} -p "${REMOTE_PORT}" "${REMOTE_USER}@${REMOTE_HOST}" "mkdir -p \"${REMOTE_PATH}\" && tar -xzf - -C \"${REMOTE_PATH}\" --strip-components=1"
fi

if [[ -n "${LOCAL_ENV_FILE}" && -f "${LOCAL_ENV_FILE}" ]]; then
  echo ">>> Enviando ${LOCAL_ENV_FILE} como .env"
  scp -P "${REMOTE_PORT}" ${SSH_OPTS} "${LOCAL_ENV_FILE}" "${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/.env"
else
  echo ">>> Aviso: arquivo .env nao enviado (LOCAL_ENV_FILE nao encontrado)."
fi

echo ">>> Executando comandos remotos"
ssh ${SSH_OPTS} -p "${REMOTE_PORT}" "${REMOTE_USER}@${REMOTE_HOST}" <<EOF
cd "${REMOTE_PATH}"
composer install --no-dev --optimize-autoloader --prefer-dist
php spark migrate --all --force
php spark cache:clear
EOF

echo ">>> Deploy concluido em ${REMOTE_HOST}:${REMOTE_PATH}"
