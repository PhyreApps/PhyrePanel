# syntax=docker/dockerfile:1

FROM ubuntu:22.04 AS production

RUN apt-get update && apt-get install -y wget
RUN ls -la
RUN wget https://raw.githubusercontent.com/PhyreApps/PhyrePanel/main/installers/install.sh -O phyre-install.sh
RUN chmod +x phyre-install.sh
RUN ./phyre-install.sh

COPY entrypoint.sh /usr/local/phyre/entrypoint.sh

ENTRYPOINT ["sh","/usr/local/phyre/entrypoint.sh"]
