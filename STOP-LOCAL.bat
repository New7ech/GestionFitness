@echo off
setlocal
cd /d "%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -File ".\scripts\windows\stop-local.ps1"
if errorlevel 1 (
  echo.
  echo Stop failed.
)
pause
