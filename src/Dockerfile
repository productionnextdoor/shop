FROM pmdocker.azurecr.io/pm-shopware6:php8.3

ADD ./src/public /var/www/html


#nginx confs
#COPY ./src/configs/.htpasswd /etc/nginx/.htpasswd
COPY ./src/config/nginx_stage.conf /etc/nginx/sites-enabled/default.conf

#symfony confs
ADD ./src/config/packages/framework.yml /var/www/html/config/packages/framework.yml

RUN sudo chown -R www-data:www-data /var/www/html