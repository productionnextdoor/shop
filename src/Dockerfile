FROM pmdocker.azurecr.io/pm-shopware6:php8.3

ADD ./public /var/www/html


#nginx confs
COPY ./configs/.htpasswd /etc/nginx/.htpasswd
COPY ./configs/nginx_dev.conf /etc/nginx/sites-enabled/default.conf

#symfony confs
ADD ./config/packages/framework.yml /var/www/html/config/packages/framework.yml

RUN sudo chown -R www-data:www-data /var/www/html