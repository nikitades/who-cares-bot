version: "3"
services:
  app_webapi:
    image: nikitades/whocaresbot-app-webapi
    restart: always
    environment:
      APP_ENV: prod
      DATABASE_URL: postgresql://dbuser:dbpwd@dbhost:5432/dbhost?serverVersion=13&charset=utf8
      APP_SECRET: somesecret
      BOT_TOKEN: 1778873763:AAHiHJaXiIQl1sDc66ApI9kquCL7NNvEq_g
      BOT_NAME: chatanalyticsbot
      CACHE_PERIOD: 300
      PEAK_SEARCH_PERIOD: 720
      MESSENGER_TRANSPORT_DSN: doctrine://default
      LOCK_DSN: flock
      IMAGE_RENDERER_ADDRESS: http://imagerenderer
    volumes:
      - ./logs:/app/var/log
    depends_on:
      - app_maintenance
      - database
  app_consumer_regular:
    image: nikitades/whocaresbot-app-consumer
    restart: always
    environment:
      APP_ENV: prod
      DATABASE_URL: postgresql://dbuser:dbpwd@dbhost:5432/dbhost?serverVersion=13&charset=utf8
      APP_SECRET: somesecret
      BOT_TOKEN: sometoken
      BOT_NAME: chatanalyticsbot
      CACHE_PERIOD: 300
      PEAK_SEARCH_PERIOD: 720
      MESSENGER_TRANSPORT_DSN: doctrine://default
      LOCK_DSN: flock
      IMAGE_RENDERER_ADDRESS: http://imagerenderer
    volumes:
      - ./logs:/app/var/log
    depends_on:
      - app_maintenance
      - database
  app_consumer_slow:
    image: nikitades/whocaresbot-app-consumer-slow
    restart: always
    environment:
      APP_ENV: prod
      DATABASE_URL: postgresql://dbuser:dbpwd@dbhost:5432/dbhost?serverVersion=13&charset=utf8
      APP_SECRET: somesecret
      BOT_TOKEN: sometoken
      BOT_NAME: chatanalyticsbot
      CACHE_PERIOD: 300
      PEAK_SEARCH_PERIOD: 720
      MESSENGER_TRANSPORT_DSN: doctrine://default
      LOCK_DSN: flock
      IMAGE_RENDERER_ADDRESS: http://imagerenderer
    volumes:
      - ./logs:/app/var/log
    depends_on:
      - app_maintenance
      - database
  app_maintenance:
    image: nikitades/whocaresbot-app-maintenance
    environment:
      APP_ENV: prod
      DATABASE_URL: postgresql://dbuser:dbpwd@dbhost:5432/dbhost?serverVersion=13&charset=utf8
      APP_SECRET: somesecret
      BOT_TOKEN: sometoken
      BOT_NAME: chatanalyticsbot
      CACHE_PERIOD: 300
      PEAK_SEARCH_PERIOD: 720
      MESSENGER_TRANSPORT_DSN: doctrine://default
      LOCK_DSN: flock
      IMAGE_RENDERER_ADDRESS: http://imagerenderer
    volumes:
      - ./logs:/app/var/log
    depends_on:
      - database
  nginx:
    image: nikitades/whocaresbot-nginx
    ports:
      - 8080:80
    volumes:
      - ./webapi/var/nginx/log:/var/log/nginx
    depends_on:
      - app_webapi
    restart: always
  imagerenderer:
    image: nikitades/whocaresbot-imagerenderer
    restart: always
    environment:
      NODE_ENV: production
      MAIN_APP_ADDRESS: http://nginx
  database:
    image: postgres:13-alpine
    restart: always
    environment:
      POSTGRES_USER: whocaresbot
      POSTGRES_PASSWORD: whocaresbot
    volumes:
      - ./webapi/docker/init-user-db.sh:/docker-entrypoint-initdb.d/init-user-db.sh
      - ./database:/var/lib/postgresql/data