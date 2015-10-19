#!/bin/bash
echo "**************************************************************************"
echo "* Updating composer for the armimport package                            *"
echo "**************************************************************************"
composer update
mkdir -p dist
echo "**************************************************************************"
echo "* Building the dist/armimport.phar file                                  *"
echo "**************************************************************************"
vendor/bin/box build
echo "**************************************************************************"
echo "* Note: php.ini must have phar.readonly = Off set to build the phar file *"
echo "**************************************************************************"
