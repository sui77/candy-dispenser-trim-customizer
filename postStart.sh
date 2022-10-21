#!/bin/sh

groupmod -g 117 docker
chmod 777 /app/blender
mkdir /app/blender/files
chmod 777 /app/files
