@echo off

if not exist "%cd%/bin" goto :start-error
if not exist "%cd%/wp-notif-bell.php" goto :start-error
goto :not-error

:start-error
echo Please run this bash from plugin directory
exit 1

:not-error
echo WP Notif Bell starter
echo ---------------------
echo [1] build all (styles + scripts)
echo [2] build styles
echo [3] build rtl styles
echo [4] build styles + rtl
echo [5] build scripts
echo [0] exit

set /p "opt=Choose option: "

if "%opt%" == "1" (
    call "%cd%/bin/css-build.cmd"
    call "%cd%/bin/rtlcss-build.cmd"
    call "%cd%/bin/script-build.cmd"
) else if "%opt%" == "2" (
    call "%cd%/bin/css-build.cmd"
) else if "%opt%" == "3" (
    call "%cd%/bin/rtlcss-build.cmd"
) else if "%opt%" == "4" (
    call "%cd%/bin/css-build.cmd"
    call "%cd%/bin/rtlcss-build.cmd"
) else if "%opt%" == "5" (
    call "%cd%/bin/script-build.cmd"
) else if "%opt%" == "0" (
    exit 0
) else (
    echo [error] Could not find this option from list!
    exit 0
)
