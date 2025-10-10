FROM nginx

RUN rm /etc/nginx/conf.d/default.conf
COPY config/duktig.conf /etc/nginx/conf.d/default.conf
COPY config/fastcgi_params /etc/nginx/fastcgi_params

RUN groupadd -g 998 vboxsf
RUN usermod -a -G vboxsf root
