::@echo off

where npm >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo You need to install npm first!
    exit 1
)

if not exist .\assets\rollup.config.js (
    echo Can't find rollup.config.js
    exit 1
)

echo Building script files ...
npm --prefix ./assets run build