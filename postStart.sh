#!/bin/sh

groupmod -g 119 docker
chmod 777 /app/blender

rm /app/blender/*.blend
rm /app/blender/*.py