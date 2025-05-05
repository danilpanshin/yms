# app



## Getting started

Makefile 

```
make help
```


```shell
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
```