$ErrorActionPreference = "Stop"

$projectRoot = Resolve-Path (Join-Path $PSScriptRoot "..\..")
Set-Location $projectRoot

docker compose stop
if ($LASTEXITCODE -ne 0) {
    throw "Impossible d'arreter les conteneurs."
}

docker compose ps
Write-Host "`nServices arretes."
