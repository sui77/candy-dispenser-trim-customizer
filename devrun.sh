#!/bin/bash


docker run \
  -d \
  --rm \
  --name redis \
  -p 172.17.0.1:6379:6379 \
  redis


#docker build -t test .
docker run -it \
  --privileged \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v /data/3dsuili:/app \
  -v /tmp/mdc:/app/blender \
  -e HOSTBDIR=/tmp/mdc \
  -p 84:80 \
  test