#!/bin/bash

docker rm -f `docker ps -a -q`
docker rmi -f kshell

docker build . -t kshell
