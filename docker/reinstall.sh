# stop all containers
docker stop $(docker ps -aq)

# remove all containers
docker rm -f $(docker ps -aq)

#remove all images
docker image rm $(docker images -q)

#remove all unused containers, networks, images, and volumes
docker system prune -f

#remove all unused volumes
docker volume prune -f

#remove all unused networks
docker network prune -f

#remove all builds
docker builder prune -f

#remove all completed builds
docker builder prune -a -f

# install new
docker compose up -d
