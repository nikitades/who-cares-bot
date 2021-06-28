# Usage
# make - compile and push all the images

.DEFAULT_GOAL := build

SERVICES_LIST = app_webapi app_consumer_regular app_consumer_slow app_maintenance app_scheduler nginx imagerenderer database redis

build: down build_base push_base build_app push_app prune

down:
	docker-compose -f docker-compose.dev.yml down

build_base:
	docker-compose -f docker-compose.dev.yml build app
	
push_base:
	docker-compose -f docker-compose.dev.yml push app
	
build_app:
	docker-compose -f docker-compose.dev.yml build --parallel ${SERVICES_LIST}

push_app:
	docker-compose -f docker-compose.dev.yml push app_webapi ${SERVICES_LIST}

prune:
	docker system prune -af