#!/bin/bash

cd /var/www/html || exit 1
chmod -R 777 storage

# This needs to be created at buildtime via codebuild! And key:generate will no longer be necessary
cp .env.example .env
php artisan key:generate

# Configure Apache configuration per environment
if [ "${DEPLOYMENT_GROUP_NAME}" == "test-pipeline-codedeploy-group-prod" ]; then
  a2ensite prod
  rm dev.conf
elif [ "${DEPLOYMENT_GROUP_NAME}" == "test-pipeline-codedeploy-group-prod" ]; then
  a2ensite dev
  rm prod.conf
fi