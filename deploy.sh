#!/bin/bash
# Deploy-Script für EduBank
# Push auf GitHub und Pull auf Server

set -e

BRANCH="feature/bootstrap5-redesign"
SERVER="carl_edubank@srv.solidcode.at"
REMOTE_PATH="/var/www/clients/client1/web169/web"

echo "=== EduBank Deploy ==="

# 1. Push zu GitHub
echo "→ Push zu GitHub..."
git push origin $BRANCH

# 2. Pull auf Server
echo "→ Pull auf Server..."
ssh $SERVER "cd $REMOTE_PATH && git pull origin $BRANCH"

# 3. Cache leeren (CakePHP)
echo "→ Cache leeren..."
ssh $SERVER "rm -rf $REMOTE_PATH/tmp/cache/models/* $REMOTE_PATH/tmp/cache/persistent/* 2>/dev/null || true"

echo "=== Fertig! ==="
echo "→ https://edubank.solidcode.at"
