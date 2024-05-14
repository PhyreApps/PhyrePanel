docker compose up --build --force-recreate
docker tag phyre-panel:latest bobicloudvision/phyre-panel:latest
docker push bobicloudvision/phyre-panel:latest
