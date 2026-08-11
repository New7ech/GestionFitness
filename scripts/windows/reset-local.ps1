$ErrorActionPreference = "Stop"

$projectRoot = Resolve-Path (Join-Path $PSScriptRoot "..\..")
Set-Location $projectRoot

docker compose down -v --remove-orphans
if ($LASTEXITCODE -ne 0) {
    throw "La reinitialisation a echoue."
}

Write-Host "`nReinitialisation terminee (volumes supprimes)."
Write-Host "Relance INSTALL-LOCAL.bat pour reinstaller."
