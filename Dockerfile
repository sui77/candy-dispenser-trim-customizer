FROM webdevops/php-nginx:8.0-alpine

WORKDIR /app
COPY . ./

RUN apk add docker
RUN chmod -R 777 /app/blender
RUN groupmod -g 117 docker
RUN addgroup application docker
