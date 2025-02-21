FROM alpine:3.21.3

RUN apk update && \
    apk add php composer imagemagick php-pdo php-dom php-intl php-session php-tokenizer php-xml php-pdo_sqlite php-gd php-simplexml

RUN cd /var/ && mkdir www && \
    composer create-project "typo3/cms-base-distribution:^13.4" www && \
    cd www && \
    composer require helhum/typo3-console

EXPOSE 8080
