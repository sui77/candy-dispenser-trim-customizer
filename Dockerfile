FROM webdevops/php-nginx:8.0-alpine

WORKDIR /app
COPY . ./

RUN apk add docker

RUN chmod 755 /app/postStart.sh
RUN chmod -R 777 /app/blender
RUN groupmod -g 119 docker
RUN addgroup application docker
