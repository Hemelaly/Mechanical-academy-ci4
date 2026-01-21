$ErrorActionPreference = 'Stop'

$commitMessage = Read-Host 'Commit message'
if ([string]::IsNullOrWhiteSpace($commitMessage)) {
    Write-Error 'Commit message is required.'
    exit 1
}

$remoteName = 'origin'
$branchName = 'main'
$sshHost = '168.231.113.60'
$sshUser = 'mechanical-academy'
$repoPath = '/home/mechanical-academy/htdocs/academy.mechanical.co.mz/'

$pending = git status --porcelain
$hasChanges = $pending -and $pending.Length -gt 0

if ($hasChanges) {
    git add -A
    git commit -m $commitMessage
} else {
    Write-Host 'No local changes. Skipping commit.'
}

git push $remoteName $branchName

$sshTarget = "$sshUser@$sshHost"
$deployCmd = @"
cd $repoPath && \
  git pull $remoteName $branchName && \
  composer install --no-dev -o && \
  npm install && \
  npm run build
"@.Trim()

ssh -o StrictHostKeyChecking=accept-new $sshTarget $deployCmd

Write-Host 'Deploy finished.'
