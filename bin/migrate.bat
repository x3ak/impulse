mysql -u root -plocal -e "SET CHARACTER SET 'utf8';"
mysql -u root -plocal -e "SET NAMES 'utf8';"
mysql -u root -plocal -e "DROP DATABASE IF EXISTS devavia;"
mysql -u root -plocal -e "CREATE DATABASE `devavia` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;"
mysql -u root -plocal -e "use devavia; source ../scripts/sql/dump.sql;"

php ../scripts/migration.php