FROM alpine:3.21.3

ENV WEBP_VERSION=1.4.0
ENV IM_VERSION=7.1.1-41

RUN apk update && \
    apk add php composer php-pdo php-dom php-intl php-session php-tokenizer php-xml php-pdo_sqlite php-gd php-simplexml

RUN cd /var/ && mkdir www && \
    composer create-project "typo3/cms-base-distribution:^13.4" www && \
    cd www && \
    composer require helhum/typo3-console

RUN apk add build-base pkgconf libpng-dev libjpeg-turbo-dev tiff-dev libheif-dev curl

RUN curl -L https://storage.googleapis.com/downloads.webmproject.org/releases/webp/libwebp-$WEBP_VERSION.tar.gz \
    -o /tmp/libwebp-$WEBP_VERSION.tar.gz
RUN cd /tmp && tar xvf libwebp-$WEBP_VERSION.tar.gz
RUN cd /tmp/libwebp-$WEBP_VERSION \
 && ./configure \
 && make \
 && make install

RUN curl -L https://download.imagemagick.org/archive/releases/ImageMagick-$IM_VERSION.tar.xz \
    -o /tmp/ImageMagick-$IM_VERSION.tar.xz
RUN cd /tmp && tar xvf ImageMagick-$IM_VERSION.tar.xz
# would like to add: --with-jxl
RUN cd /tmp/ImageMagick-$IM_VERSION \
 && ./configure --with-webp --with-tiff --with-png --with-heic \
 && make \
 && make install

RUN cd /tmp \
 && rm -rf libwebp-$WEBP_VERSION* \
 && rm -rf ImageMagick-$IM_VERSION*

RUN ldconfig /usr/local/lib

EXPOSE 8080
