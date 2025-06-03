.ONESHELL:
.DEFAULT_GOAL := help
.PHONY: artisan

ENV_FILE=--env-file ./.env

ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))

help:
	@echo "Usage: make [artisan, composer, build, restart, status, up, down]"
	@echo "artisan and composer can handle args ( make [artisan, composer] [args...] )"

artisan:
	docker-compose $(ENV_FILE) -p yms exec app /var/www/html/artisan $(ARGS)

composer:
	docker-compose $(ENV_FILE) -p yms exec app /usr/local/bin/composer $(ARGS)

build:
	docker-compose $(ENV_FILE) -p yms down
	docker-compose $(ENV_FILE) -p yms up -d --build

restart:
	docker-compose $(ENV_FILE) -p yms down
	docker-compose $(ENV_FILE) -p yms up -d

status:
	docker-compose $(ENV_FILE) -p yms ps

up:
	docker-compose $(ENV_FILE) -p yms up -d

down:
	docker-compose $(ENV_FILE) -p yms down

clear_all:
	docker-compose $(ENV_FILE) -p yms down
	docker system prune -f --all

%:
	@:
