::@echo off

where npm >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo You need to install npm first!
    exit 1
)

where npx sass >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo Sass wasn't found!
    set /p "iss=Are u gonna install sass? (y/n): "

    if "%iss%" == "y" (
        echo Installing from npm ...
        npm i -g sass
    )
)

set astDir=".\assets\sass"
set dstDir=".\assets\dist\css"

if not exist %astDir% (
    echo Can't find sass folder.
    exit 1
)

for /R %%f in (%astDir%\*.sass) do (
    echo Compiling %%~nf.sass
    sass "%%f" "%dstDir%\%%~nf.css"
)