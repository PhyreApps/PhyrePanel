sudo apt-get update -y
sudo apt-get install ca-certificates curl -y
sudo install -m 0755 -d /etc/apt/keyrings
sudo curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
sudo chmod a+r /etc/apt/keyrings/docker.asc

# Add the repository to Apt sources:
echo \
  "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu \
  $(. /etc/os-release && echo "$VERSION_CODENAME") stable" | \
  sudo tee /etc/apt/sources.list.d/docker.list > /dev/null
sudo apt-get update

sudo apt-get install docker-compose docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin -y


# Enable email ports
ufw allow 25
ufw allow 587
ufw allow 465
ufw allow 993

docker exec -it xx setup config dkim
docker exec -ti xx setup email add xx@domain.ai passwd123
docker exec -ti xx setup email add user@domain.ai passwd123
docker exec -ti x setup alias add postmaster@domain.ai user@domain.ai

echo "Done!"


