FROM nginx:1.10

ADD ./vhost.conf /etc/nginx/conf.d/default.conf
WORKDIR /var/www

# Make the container's port 80 available to the outside world
EXPOSE 80
