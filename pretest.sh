#! /bin/bash

set -e

if [ -d "./tests/_tmp/drupal" ]; then
    exit 0
fi

mkdir -p ./tests/_tmp
cd ./tests/_tmp
wget https://ftp.drupal.org/files/projects/drupal-10.0.0.tar.gz
tar -xf drupal-10.0.0.tar.gz
mv drupal-10.0.0 drupal
cd drupal
php ../../../scripts/dump_script.php node
