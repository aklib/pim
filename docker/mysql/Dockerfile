FROM mysql:latest
ENV MYSQL_ROOT_PASSWORD test123Pa
ENV MYSQL_DATABASE pim
ENV MYSQL_USER _pimuser_
ENV MYSQL_PASSWORD test123
COPY pim.sql /docker-entrypoint-initdb.d/
EXPOSE 3316