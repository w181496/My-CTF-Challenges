FROM php:7-apache
#RUN apt-get update

RUN apt update

COPY index.php /var/www/html/index.php
COPY home.php /var/www/html/home.php
COPY about.php /var/www/html/about.php
COPY cat.php /var/www/html/cat.php
COPY asset /var/www/html/asset

ADD flag /flag
RUN chmod 600 /flag
RUN chmod 700 /tmp
ADD readflag.c /readflag.c
RUN apt -y install gcc && \
gcc /readflag.c -o /readflag && \
chmod +s /readflag


#RUN apachectl start
