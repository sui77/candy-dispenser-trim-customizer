#!/bin/bash


#docker run \
#  -d \
#  --rm \
#  --name redis \
#  -p 172.17.0.1:6379:6379 \
# redis


docker build -t testcd .
docker run -it \
  --privileged \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v /data/candy-dispenser-trim-customizer:/app \
  -v /tmp/mdc:/app/blender \
  -e HOSTBDIR=/tmp/mdc \
  -p 86:80 \
  testcd