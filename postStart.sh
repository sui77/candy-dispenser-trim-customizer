#!/bin/sh

groupmod -g 119 docker

mkdir -p /app/blender/files
chmod -R 777 /app/blender
cp -R /app/static/* /app/blender

