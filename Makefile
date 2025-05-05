.ONESHELL:
.DEFAULT_GOAL := help
.PHONY: artisan

ENV_FILE=--env-file ./src/.env

ARGS := $(wordlist 2,$(words $(MAKECMDGOALS)),$(MAKECMDGOALS))

help:
	@echo "Usage: make [artisan, composer, build, restart, status, up, down] [args...]"

artisan:
	docker-compose $(ENV_FILE) run --name artisan -it --rm artisan $(ARGS)

composer:
	docker-compose $(ENV_FILE) run --name composer -it --rm composer $(ARGS)

build:
	docker-compose $(ENV_FILE) down
	docker-compose $(ENV_FILE) -p yms up -d --build

restart:
	docker-compose $(ENV_FILE) down
	docker-compose $(ENV_FILE) -p yms up -d

status:
	docker-compose $(ENV_FILE) ps

up:
	docker-compose $(ENV_FILE) -p yms up -d

down:
	docker-compose $(ENV_FILE) down

clear_all:
	docker-compose $(ENV_FILE) down
	docker system prune -f --all

%:
	@:
