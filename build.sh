#!/usr/bin/env bash

TARGET_DIR=${1:-.}

docker-compose build
docker-compose -f docker-compose.yml up -d app
docker-compose -f docker-compose.yml run composer install --no-dev
docker-compose -f docker-compose.yml run npm run build
docker-compose -f docker-compose.yml run app tar -zcvf app.tar.gz --files-from=buildfiles.txt
docker cp $(docker-compose ps -q app):/var/www/html/app.tar.gz "$TARGET_DIR/app.tar.gz"
docker-compose down -v
