#!/bin/bash

DOMAIN=$1

mysql --user=root -p --host=127.0.0.1 --execute='
CREATE DATABASE IF NOT EXISTS tatagtest;
CREATE DATABASE IF NOT EXISTS tatagtestdtd;
exit
'

curl "http://$DOMAIN/api/tools/db_init.php?db=tatagtest,tatagtestdtd";

# if you you don't have 

composer install

# run tests



