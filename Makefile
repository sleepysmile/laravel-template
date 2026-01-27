docker_compose_bin := $(shell command -v docker-compose 2> /dev/null)

up:
	$(docker_compose_bin) up -d

down:
	$(docker_compose_bin) down

build:
	$(docker_compose_bin) build

restart: down up