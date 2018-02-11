live: purge-containers
	docker-compose up -d --build 

interative: purge-containers
	docker-compose up --build

stop:
	docker-compose stop

clean-install: app-install live symlink layout-images wait database-migrate database-seed
 
app-install: folder-structure composer-install npm-install layout-images

app-install-dev: folder-structure composer-install-dev npm-install-dev ssh-keygen

###########
# HELPERS #
###########

layout-images:
	cp -r resources/assets/images/* storage/app/public/images/main/

symlink:
	docker exec lan_manager_app php artisan storage:link

database-migrate:
	docker exec lan_manager_app php artisan migrate

database-seed:
	docker exec lan_manager_app php artisan db:seed

database-rollback:
	docker exec lan_manager_app php artisan db:rollback

folder-structure:
	mkdir storage/app/public/images/gallery/
	mkdir storage/app/public/images/events/
	mkdir storage/app/public/images/venues/
	mkdir storage/app/public/images/main/
	chmod 777 bootstrap/cache/
	chmod 777 storage/

ssh-keygen:
	sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout certs/nginx.key -out certs/nginx.crt

composer-install:
	docker run --rm --interactive --tty \
    --volume $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/app \
    --user $(id -u):$(id -g) \
    composer install --ignore-platform-reqs --no-scripts

composer-install-dev:
	docker run --rm --interactive --tty \
    -v $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/app \
    --user $(id -u):$(id -g) \
    composer install --ignore-platform-reqs --no-scripts --dev

npm-install:
	docker run -it --rm --name js-maintainence \
	-v $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/usr/src/app \
	-w /usr/src/app \
	node:8 npm install && gulp --production

npm-install-dev:
	docker run -it --rm --name js-maintainence-dev \
	-v $(shell dirname $(realpath $(lastword $(MAKEFILE_LIST)))):/usr/src/app \
	-w /usr/src/app \
	node:8 npm install && gulp

purge-containers:
	docker-compose -p lan_manager stop
	docker-compose -p lan_manager rm -vf

wait:
	sleep 20


###############
# DANGER ZONE #
###############
purge-all: stop purge-containers
	echo 'This is dangerous!'
	echo 'This will totally remove all data and information stored in your app!'
	echo 'do you want to continue? (Y/N)'
	
	sudo rm -rf vendor/
	sudo rm -rf node_modules/
	sudo rm -rf public/css/*
	sudo rm -rf storage/app/public/images/gallery
	sudo rm -rf storage/app/public/images/events
	sudo rm -rf storage/app/public/images/venues
	sudo rm -rf storage/app/public/images/main
	sudo rm -rf storage/logs/*
	sudo rm -rf storage/framework/cache/*
	sudo rm -rf storage/framework/views/*
	sudo rm -rf storage/framework/sessions/*
	sudo rm -rf storage/debugbar/*
	sudo rm -rf bootstrap/cache/*
	sudo rm public/storage

	docker rm lan_manager_server
	docker rm lan_manager_app
	docker rm lan_manager_database
	docker rmi manager_server
	docker rmi manager_app
	docker rmi manager_database
	docker volume rm lan_manager_db