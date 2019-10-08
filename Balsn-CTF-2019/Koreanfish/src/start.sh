# start apache
apachectl start

rm /var/www/html/index.html

# start flask
su - www-data -s /bin/bash -c 'python3 /var/www/flask/main.py &>/dev/null &' &

# show http log
tail -f /var/log/apache2/access.log
