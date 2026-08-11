$ErrorActionPreference = "Stop"

function Ensure-Command {
    param([string]$Name)
    if (-not (Get-Command $Name -ErrorAction SilentlyContinue)) {
        throw "Commande '$Name' introuvable."
    }
}

function Ensure-DockerRunning {
    try {
        docker info *> $null
    }
    catch {
        throw "Docker Desktop n'est pas demarre."
    }
}

$projectRoot = Resolve-Path (Join-Path $PSScriptRoot "..\..")
Set-Location $projectRoot

Ensure-Command -Name "docker"
Ensure-DockerRunning

docker compose up -d
if ($LASTEXITCODE -ne 0) {
    throw "Impossible de demarrer les conteneurs."
}

docker compose ps

Write-Host "`nApplication : http://localhost:8080"
Write-Host "phpMyAdmin  : http://localhost:8081"
Write-Host "MailHog     : http://localhost:8025"
