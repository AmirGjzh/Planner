#!/bin/bash
set -e

composer install --no-interaction --no-progress --prefer-dist
npm install --no-progress

exec "$@"