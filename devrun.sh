#!/bin/bash

#docker build -t test .
docker run -it \
  --privileged \
  -v /var/run/docker.sock:/var/run/docker.sock \
  -v /root/candydispenser:/app \
  -v /tmp/modelcustomizer:/app/blender \
  -p 80:80 \
  test