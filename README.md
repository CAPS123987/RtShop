# NetteSqlDocker
Template for creating nette aplication with sql and phpadmin


## 1.
change folder name to **PROJECT**

change docker-compose.yml volumes to **PROJECT**
 
change 000-default.conf to **PROJECT**

change in dockerfile RUN echo "ServerRoot to '/workspaces/**PROJECT**


mkdir temp
mkdir log
chmod -R a+rw temp log

change salt DbAuth.php

may run 

sudo sh buildphp.sh


to start -> docker-compose up
