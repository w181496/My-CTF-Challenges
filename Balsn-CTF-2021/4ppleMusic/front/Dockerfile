FROM nimmis/apache-php7

COPY index.html /var/www/html/index.html
COPY index.php /var/www/html/index.php
COPY get.php /var/www/html/get.php
COPY header.php /var/www/html/header.php
COPY footer.php /var/www/html/footer.php
COPY phpinfo.php /var/www/html/phpinfo.php
COPY img /var/www/html/img

RUN apt update && apt install -y php7.0-xml
