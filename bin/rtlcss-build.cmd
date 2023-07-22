::@echo off

where npm >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo You need to install npm first!
    exit 1
)

where npx rtlcss >nul 2>nul
if %ERRORLEVEL% neq 0 (
    echo RTLCss wasn't found!
    set /p "iss=Are u gonna install rtlCss? (y/n): "

    if "%iss%" == "y" (
        echo Installing from npm ...
        npm i -g rtlcss
    )
)

set fsDir=".\assets\dist\css"

if not exist %fsDir% (
    echo Can't find css folder. Please first build css file.
    exit 1
)

if not exist .\assets\rtlcss.json (
    echo Can't find rtlcss.json
    exit 1
)

for /R %fsDir% %%f in (*.rtl.css) do (
    del /f %%f
    echo "%%~nf.css" removed.
)

echo Building rtl-css files ...
npx rtlcss -d %fsDir% -c .\assets\tsconfig.json