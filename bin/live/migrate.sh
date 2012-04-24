#!/bin/sh
echo Migration started...

export APPLICATION_ENV=production
php ../../scripts/migration.php

echo Done.