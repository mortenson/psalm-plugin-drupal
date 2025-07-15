#! /bin/bash

set -e

if [ -d "./tests/_tmp/drupal" ]; then
    exit 0
fi

mkdir -p ./tests/_tmp
cd ./tests/_tmp
wget https://ftp.drupal.org/files/projects/drupal-11.1.6.tar.gz
tar -xf drupal-11.1.6.tar.gz
mv drupal-11.1.6 drupal
cd drupal
php ../../../scripts/generate_test_connection.php
php ../../../scripts/dump_script.php node
