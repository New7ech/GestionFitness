$ErrorActionPreference = "Stop"

function Write-Step {
    param([string]$Message)
    Write-Host "`n==> $Message" -ForegroundColor Cyan
}

function Ensure-Command {
    param([string]$Name)
    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        throw "Commande '$Name' introuvable. Installe Docker Desktop puis reessaie."
    }
}

function Ensure-DockerRunning {
    try {
        docker info *> $null
    }
    catch {
        throw "Docker Desktop n'est pas demarre. Ouvre Docker Desktop puis relance le script."
    }
}

function Invoke-AppCommandWithRetry {
    param(
        [string]$Label,
        [string]$Command,
        [int]$Attempts = 12,
        [int]$SleepSeconds = 5
    )

    for ($try = 1; $try -le $Attempts; $try++) {
        docker compose exec -T app sh -lc $Command
        if ($LASTEXITCODE -eq 0) {
            return
        }

        if ($try -lt $Attempts) {
            Write-Host "Tentative $try/$Attempts echouee pour '$Label'. Nouvelle tentative dans $SleepSeconds s..." -ForegroundColor Yellow
            Start-Sleep -Seconds $SleepSeconds
        }
    }

    throw "Echec de la commande '$Label' apres $Attempts tentatives."
}

function Wait-ForMySqlHealth {
    param(
        [int]$MaxAttempts = 60,
        [int]$SleepSeconds = 2
    )

    $containerId = ""
    for ($try = 1; $try -le $MaxAttempts; $try++) {
        $containerId = (docker compose ps -q mysql).Trim()
        if ($containerId) {
            break
        }
        Start-Sleep -Seconds $SleepSeconds
    }

    if (-not $containerId) {
        throw "Le conteneur mysql est introuvable."
    }

    for ($try = 1; $try -le $MaxAttempts; $try++) {
        $status = (docker inspect --format "{{.State.Health.Status}}" $containerId 2>$null).Trim()
        if ($status -eq "healthy") {
            return
        }
        Start-Sleep -Seconds $SleepSeconds
    }

    throw "MySQL n'est pas devenu healthy dans les delais."
}

$projectRoot = Resolve-Path (Join-Path $PSScriptRoot "..\..")
Set-Location $projectRoot

Write-Step "Verification des prerequis"
Ensure-Command -Name "docker"
Ensure-DockerRunning

if (-not (Test-Path ".env")) {
    Write-Step "Creation de .env"
    Copy-Item ".env.example" ".env"
}

Write-Step "Build et demarrage des conteneurs"
docker compose up -d --build
if ($LASTEXITCODE -ne 0) {
    throw "docker compose up -d --build a echoue."
}

Write-Step "Attente de MySQL"
Wait-ForMySqlHealth

$appKeyLine = Select-String -Path ".env" -Pattern "^APP_KEY=" | Select-Object -First 1
if (-not $appKeyLine -or $appKeyLine.Line.Trim() -eq "APP_KEY=") {
    Write-Step "Generation de APP_KEY"
    Invoke-AppCommandWithRetry -Label "key:generate" -Command "php artisan key:generate --force" -Attempts 3 -SleepSeconds 3
}

Write-Step "Migration de la base"
Invoke-AppCommandWithRetry -Label "migrate" -Command "php artisan migrate --force"

Write-Step "Seed de la base"
Invoke-AppCommandWithRetry -Label "db:seed" -Command "php artisan db:seed --force"

Write-Step "Lien storage"
Invoke-AppCommandWithRetry -Label "storage:link" -Command "php artisan storage:link || true" -Attempts 1

Write-Step "Etat des services"
docker compose ps

Write-Host "`nInstallation terminee." -ForegroundColor Green
Write-Host "Application : http://localhost:8080"
Write-Host "phpMyAdmin  : http://localhost:8081"
Write-Host "MailHog     : http://localhost:8025"
Write-Host "`nIdentifiants DB Docker (defaut):"
Write-Host "- Host: localhost (depuis Windows) / mysql (depuis les conteneurs)"
Write-Host "- Port: 3307"
Write-Host "- Base: laravel"
Write-Host "- User: laravel"
Write-Host "- Pass: secret"
