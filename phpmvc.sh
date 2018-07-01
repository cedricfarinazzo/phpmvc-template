#!/bin/bash

CWD=$(pwd)

cd /tmp
rm -rf /tmp/phpmvc-template/

git clone https://github.com/cedricfarinazzo/phpmvc-template.git
cd /tmp/phpmvc-template/
git submodule update --init
git pull origin master

rm -rf .git
rm -f README.md
rm -rf .gitmodules

mv /tmp/phpmvc-template/* $CWD
mv /tmp/phpmvc-template/.* $CWD

rm -rf /tmp/phpmvc-template
