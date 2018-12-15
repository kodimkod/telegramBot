set COMPOSER_VERSION=download/1.3.0/
set GIT_SSH=c:\Program Files (x86)\putty\plink.exe
set PHPBIN=c:\xampp\php\php.exe
%PHPBIN% .\composer.phar dumpautoload -o
REM del .\composer.phar