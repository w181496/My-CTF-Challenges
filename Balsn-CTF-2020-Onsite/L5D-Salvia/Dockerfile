FROM nimmis/apache-php7

RUN mkdir /score
RUN mkdir /round
RUN mkdir /payload
RUN mkdir /bot
RUN mkdir /bot/output
RUN mkdir /bot/gen
RUN mkdir /var/www/html/fine
RUN mkdir /log

COPY reverse.jpg /var/www/html/reverse.jpg
COPY robots.txt /var/www/html/robots.txt
COPY web /var/www/html
COPY token_list /.token_list
COPY index.php /bot/index.php
COPY gen /bot/gen
COPY update.php /bot/update.php

# disable opcache
COPY opcache.ini /etc/php/7.0/cli/conf.d/10-opcache.ini

# fine
COPY fine.html /var/www/html/fine/index.html
COPY fine.css /var/www/html/fine/fine.css

# init round number
RUN echo 0 > /round/number.txt
RUN chmod 777 /round/number.txt

RUN chmod 777 /bot/gen
RUN chmod 777 /payload
RUN chmod 777 /score
RUN chmod 777 /round
RUN chmod 777 /bot/output
RUN chmod 777 /log

# generate testcase & answer
RUN cd /bot/gen && /bin/su - www-data -s /usr/bin/python /bot/gen/gen.py

# set crontab
RUN printf  "*/5 * * * * /bin/su - www-data -s /usr/bin/php /bot/update.php;\n" | crontab -

# RUN /etc/init.d/cron start
# RUN apachectl start
# RUN rm /var/www/htm/index.html

