services:
    minecraft-server:
        image: itzg/minecraft-server
        tty: true
        stdin_open: true
        ports:
            - "25565:25565"
        environment:
            EULA: "TRUE"
            GAMEMODE: survival
            DIFFICULTY: normal
