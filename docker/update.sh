docker compose down
docker compose up -d
docker update --restart unless-stopped $(docker ps -q)
