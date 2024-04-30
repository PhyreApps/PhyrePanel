apt-get install apache2-utils -y
ab -V
ab -n 1000 -c 100 https://bojkata.phyrecloud.com/
