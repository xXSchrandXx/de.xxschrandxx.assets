set PACKAGE_NAME=de.xxschrandxx.assets
set PACKAGE_TYPES=(acptemplates files_assets files_wcf templates)

call composer install

call tsc

for %%i in %PACKAGE_TYPES% do (
    del .\%%i.tar
    7z a -ttar -mx=9 .\%%i.tar .\%%i\*
)

del %PACKAGE_NAME%.tar
del %PACKAGE_NAME%.tar.gz
7z a -ttar -mx=9 %PACKAGE_NAME%.tar .\* -x!acptemplates -x!files_assets -x!files_wcf -x!templates -x!%PACKAGE_NAME%.tar -x!%PACKAGE_NAME%.tar.gz -x!.git -x!.gitignore -x!.gitattributes -x!make.sh -x!make.bat -x!.github -x!php_cs.dist -x!.phpcs.xml -x!Readme.md -x!pictures -x!node_modules -x!package-lock.json -x!package.json -x!tsconfig.json -x!ts -x!constants.php -x!composer.json -x!composer.lock
7z a %PACKAGE_NAME%.tar.gz %PACKAGE_NAME%.tar
del ".\%PACKAGE_NAME%.tar"

for %%i in %PACKAGE_TYPES% do (
    del .\%%i.tar
)

del .\files_wcf\js\
