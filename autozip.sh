#!/bin/bash

read -p "enter version:" VERSION

if [ ! -d "./Builds/PSN_GameListAPI_4_WordPress/" ];then
  mkdir -p ./Builds/PSN_GameListAPI_4_WordPress/
  else
  echo "delete old now ~"
fi

cd ./Builds/PSN_GameListAPI_4_WordPress/

rm -rf *

cp -r ../../Functions ./Functions
cp ../../PSNGameListAPI.php ./PSNGameListAPI.php
cp ../../README.md ./README.md
cp ../../uninstall.php ./uninstall.php
# builds
cd ../

zip -q -r "PSN_GameListAPI_4_WordPress"${VERSION}.zip ./PSN_GameListAPI_4_WordPress

read -p "any key to quit" quitkey
