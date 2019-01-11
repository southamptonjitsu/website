#!/usr/bin/env bash

BUILD_DIR=$1
SSH_HOST=$2
SSH_USER=$3
SSH_PASS=$4

NOW=`date +%Y%m%d%H%M%S%N`

ARTIFACT_NAME="app.tar.gz"

ARTIFACT_PATH="./artifacts/$ARTIFACT_NAME"
RELEASES_DIR="./releases"
DEPLOY_DIR="$RELEASES_DIR/$NOW"
SERVED_PATH="./htdocs"

echo "Copying file to remote server..."
sshpass -p $SSH_PASS scp -r "$BUILD_DIR/$ARTIFACT_NAME" $SSH_USER@$SSH_HOST:$ARTIFACT_PATH

echo "Starting release..."
sshpass -p $SSH_PASS ssh $SSH_USER@$SSH_HOST "bash -s" < ./release.sh $ARTIFACT_PATH $RELEASES_DIR $DEPLOY_DIR $SERVED_PATH
