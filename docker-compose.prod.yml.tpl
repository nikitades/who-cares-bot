version: "3"
services:
  app_webapi:
    image: nikitades/whocaresbot-app-webapi
    restart: always
    environment:
      APP_ENV: prod
      DATABASE_URL: postgresql://dbuser:dbpwd@dbhost:5432/dbhost?serverVersion=13&charset=utf8
      REDIS_DSN: redis://redis:6379
      APP_SECRET: somesecret
      BOT_TOKEN: 1778873763:AAHiHJaXiIQl1sDc66ApI9kquCL7NNvEq_g
      BOT_NAME: chatanalyticsbot
      CACHE_PERIOD: 300
      PEAK_SEARCH_PERIOD: 720
      MESSENGER_TRANSPORT_DSN: doctrine://default
      LOCK_DSN: flock
    volumes:
      - ./logs:/app/var/log
    depends_on:
      - app_maintenance
      - database
      - redis
  app_consumer_regular:
    image: nikitades/whocaresbot-app-consumer
    restart: always
    environment:
      APP_ENV: prod
      DATABASE_URL: postgresql://dbuser:dbpwd@dbhost:5432/dbhost?serverVersion=13&charset=utf8
      REDIS_DSN: redis://redis:6379
      APP_SECRET: somesecret
      BOT_TOKEN: sometoken
      BOT_NAME: chatanalyticsbot
      CACHE_PERIOD: 300
      PEAK_SEARCH_PERIOD: 720
      MESSENGER_TRANSPORT_DSN: doctrine://default
      LOCK_DSN: flock
    volumes:
      - ./logs:/app/var/log
    depends_on:
      - app_maintenance
      - database
      - redis
  app_consumer_slow:
    image: nikitades/whocaresbot-app-consumer-slow
    restart: always
    environment:
      APP_ENV: prod
      DATABASE_URL: postgresql://dbuser:dbpwd@dbhost:5432/dbhost?serverVersion=13&charset=utf8
      REDIS_DSN: redis://redis:6379
      APP_SECRET: somesecret
      BOT_TOKEN: sometoken
      BOT_NAME: chatanalyticsbot
      CACHE_PERIOD: 300
      PEAK_SEARCH_PERIOD: 720
      MESSENGER_TRANSPORT_DSN: doctrine://default
      LOCK_DSN: flock
    volumes:
      - ./logs:/app/var/log
    depends_on:
      - app_maintenance
      - database
      - redis
  app_maintenance:
    image: nikitades/whocaresbot-app-maintenance
    environment:
      APP_ENV: prod
      DATABASE_URL: postgresql://dbuser:dbpwd@dbhost:5432/dbhost?serverVersion=13&charset=utf8
      REDIS_DSN: redis://redis:6379
      APP_SECRET: somesecret
      BOT_TOKEN: sometoken
      BOT_NAME: chatanalyticsbot
      CACHE_PERIOD: 300
      PEAK_SEARCH_PERIOD: 720
      MESSENGER_TRANSPORT_DSN: doctrine://default
      LOCK_DSN: flock
  app_scheduler:
    image: nikitades/whocaresbot-app-scheduler
    environment:
      APP_ENV: prod
      DATABASE_URL: postgresql://dbuser:dbpwd@dbhost:5432/dbhost?serverVersion=13&charset=utf8
      REDIS_DSN: redis://redis:6379
      APP_SECRET: somesecret
      BOT_TOKEN: sometoken
      BOT_NAME: chatanalyticsbot
      CACHE_PERIOD: 300
      PEAK_SEARCH_PERIOD: 720
      MESSENGER_TRANSPORT_DSN: doctrine://default
      LOCK_DSN: flock
    volumes:
      - ./logs/cron/cron.log:/var/log/cron.log
    depends_on:
      - database
      - redis
  nginx:
    image: nikitades/whocaresbot-nginx
    ports:
      - 8080:80
    volumes:
      - ./var/nginx/log:/var/log/nginx
    depends_on:
      - app_webapi
    restart: always
  database:
    image: postgres:13-alpine
    restart: always
    environment:
      POSTGRES_USER: whocaresbot
      POSTGRES_PASSWORD: whocaresbot
    volumes:
      - ./docker/init-user-db.sh:/docker-entrypoint-initdb.d/init-user-db.sh
      - ./database/files:/var/lib/postgresql/data
  redis:
    image: redis:6-alpine
    volumes:
      - ./database/redis:/data
