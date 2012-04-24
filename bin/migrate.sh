#!/bin/sh
echo Loading data files ...

mysql -u interteam -p123123 -e "SET CHARACTER SET 'utf8';"
mysql -u interteam -p123123 -e "SET NAMES 'utf8';"

mysql -u interteam -p123123 -e "DROP DATABASE IF EXISTS avia;"
mysql -u interteam -p123123 -e "CREATE DATABASE avia DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;"

mysql -u interteam -p123123 -e "use avia; source ../scripts/sql/dump.sql;"

php ../scripts/migration.php

echo Done.
