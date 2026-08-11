@echo off
setlocal
cd /d "%~dp0"
powershell -NoProfile -ExecutionPolicy Bypass -File ".\scripts\windows\reset-local.ps1"
if errorlevel 1 (
  echo.
  echo Reset failed.
)
pause
