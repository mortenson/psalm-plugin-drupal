#! /bin/bash

set -e

if [ -d "./tests/_tmp/drupal" ]; then
    exit 0
fi

mkdir -p ./tests/_tmp
cd ./tests/_tmp
wget https://ftp.drupal.org/files/projects/drupal-9.1.5.tar.gz
tar -xf drupal-9.1.5.tar.gz
mv drupal-9.1.5 drupal
cd drupal
php ../../../scripts/dump_script.php node
