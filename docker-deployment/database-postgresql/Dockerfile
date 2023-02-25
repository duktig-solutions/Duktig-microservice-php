FROM postgres:14.2

ADD ./docker-entrypoint-initdb.d /docker-entrypoint-initdb.d
RUN chmod 777 /docker-entrypoint-initdb.d/*
