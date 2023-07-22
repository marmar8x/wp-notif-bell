::@echo off

where npm >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo You need to install npm first!
    exit 1
)

where npx tsc >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo Typescript wasn't found!
    set /p "iss=Are u gonna install typescript? (y/n): "

    if "%iss%" == "y" (
        echo Installing from npm ...
        npm i -g typescript
    )
)

set astDir=".\assets\script"
set dstDir=".\assets\dist\js"

if not exist %astDir% (
    echo Can't find script folder.
    exit 1
)

if not exist .\assets\tsconfig.json (
    echo Can't find tsconfig.json
    exit 1
)

echo Compiling ts files...
npx tsc --p .\assets\tsconfig.json