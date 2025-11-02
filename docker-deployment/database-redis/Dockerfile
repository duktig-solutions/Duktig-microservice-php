FROM redis:6.2.5-alpine
LABEL maintainer="Duktig Solutions <support@duktig.solutions>"
ADD ./redis.conf /etc/redis.conf
RUN chown redis:redis /etc/redis.conf

ENTRYPOINT [ "redis-server", "/etc/redis.conf" ]
