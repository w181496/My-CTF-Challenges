FROM fauria/lamp
RUN curl https://bootstrap.pypa.io/get-pip.py -o /tmp/get-pip.py
RUN python3 /tmp/get-pip.py
RUN pip3 install flask

RUN mkdir /var/www/flask
RUN mkdir /var/www/flask/error

RUN rm /var/www/html/index.html
COPY index.php /var/www/html/index.php
COPY phpinfo.php /var/www/html/phpinfo.php
COPY main.py /var/www/flask/main.py
COPY error/no_id.html /var/www/flask/error/no_id.html
COPY error/id_range.html /var/www/flask/error/id_range.html
COPY templates /var/www/flask/templates
COPY static /var/www/flask/static
COPY flag /Th1s_15_fl@g_yO

RUN chmod 600 /Th1s_15_fl@g_yO
ADD readflag.c /readflag.c
RUN apt -y install gcc cron && \
gcc /readflag.c -o /readflag && \
chmod +s /readflag

RUN rm /usr/bin/gcc* /usr/bin/g++* /usr/bin/git* /bin/sleep /usr/bin/yes

COPY start.sh /start.sh

CMD ["bash", "/start.sh"]
