# Usage
# make - compile and push all the images

.DEFAULT_GOAL := build

SERVICES_LIST = app_webapi app_consumer_regular app_consumer_slow app_maintenance app_scheduler nginx database redis

build: builx_install composer build_amd64 build_arm64

builx_install:
	docker run --privileged --rm tonistiigi/binfmt --install all

composer:
	composer install
	bin/console cache:warmup

build_amd64:
	docker buildx build --platform linux/amd64 -t nikitades/whocaresbot-app-base:latest -f docker/Dockerfile.base --push .
	docker buildx build --platform linux/amd64 -t nikitades/whocaresbot-app-webapi:latest -f docker/Dockerfile.app --push .
	docker buildx build --platform linux/amd64 -t nikitades/whocaresbot-app-consumer:latest -f docker/Dockerfile.consumer.regular --push .
	docker buildx build --platform linux/amd64 -t nikitades/whocaresbot-app-consumer-slow:latest -f docker/Dockerfile.consumer.slow --push .
	docker buildx build --platform linux/amd64 -t nikitades/whocaresbot-app-maintenance:latest -f docker/Dockerfile.maintenance --push .
	docker buildx build --platform linux/amd64 -t nikitades/whocaresbot-app-scheduler:latest -f docker/Dockerfile.scheduler --push .
	docker buildx build --platform linux/amd64 -t nikitades/whocaresbot-nginx:latest -f docker/Dockerfile.nginx --push .

build_arm64:
	docker buildx build --platform linux/arm64 -t nikitades/whocaresbot-app-base:arm-latest -f docker/Dockerfile.base-arm --push .
	docker buildx build --platform linux/arm64 -t nikitades/whocaresbot-app-webapi:arm-latest -f docker/Dockerfile.app-arm --push .
	docker buildx build --platform linux/arm64 -t nikitades/whocaresbot-app-consumer:arm-latest -f docker/Dockerfile.consumer.regular-arm --push .
	docker buildx build --platform linux/arm64 -t nikitades/whocaresbot-app-consumer-slow:arm-latest -f docker/Dockerfile.consumer.slow-arm --push .
	docker buildx build --platform linux/arm64 -t nikitades/whocaresbot-app-maintenance:arm-latest -f docker/Dockerfile.maintenance-arm --push .
	docker buildx build --platform linux/arm64 -t nikitades/whocaresbot-app-scheduler:arm-latest -f docker/Dockerfile.scheduler-arm --push .
	docker buildx build --platform linux/arm64 -t nikitades/whocaresbot-nginx:arm-latest -f docker/Dockerfile.nginx-arm --push .

down:
	docker-compose -f docker-compose.dev.yml down

prune:
	docker system prune -af