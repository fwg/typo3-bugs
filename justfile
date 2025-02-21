_list:
    just -l

ddev-clean:
    if ddev ls -j | jq '.raw | map(select(.name == "typo3-bugs"))[0].name' -e; then \
      ddev stop --unlist --remove-data --omit-snapshot typo3-bugs; \
    fi
    rm -rf .ddev var config public/* vendor
    rm -f composer.lock bin

_ddev-dirs:
    mkdir .ddev
    mkdir .ddev/web-build
    mkdir -p public/typo3
    [ -L bin ] || ln -snvf vendor/bin bin

_install-typo3 command:
    ddev {{command}} install:setup \
      --no-interaction \
      --database-driver=pdo_mysql \
      --database-user-name=db \
      --database-user-password=db \
      --database-host-name=db \
      --database-port=3306 \
      --database-name=db \
      --use-existing-database \
      --admin-user-name=admin \
      --admin-password=Admin%123 \
      --site-setup-type=site \
      --site-base-url=https://typo3-bugs.ddev.site/ \
      --site-name="Issue #..."

_ddev-config php-version env:
    ddev config \
      --php-version={{php-version}} \
      --project-type=typo3 \
      --project-name=typo3-bugs \
      --docroot=public \
      --web-environment="{{env}}"

ddev-10-4: ddev-clean _ddev-dirs _add-im7
    just _ddev-config 7.4 TYPO3_CONTEXT=Development
    ddev start
    # narrow TYPO3 version to 10.4 LTS
    ddev composer require typo3/cms-core:10.4.37
    just _install-typo3 typo3cms
    git checkout -- composer.json

ddev-11-5: ddev-clean _ddev-dirs _add-im7
    just _ddev-config 8.2 TYPO3_CONTEXT=Development
    ddev start
    # narrow TYPO3 version to 11.5 LTS
    ddev composer require typo3/cms-core:11.5.31
    just _install-typo3 typo3cms
    git checkout -- composer.json

ddev-12-4: ddev-clean _ddev-dirs _add-im7
    just _ddev-config 8.2 TYPO3_CONTEXT=Development,TYPO3_PATH_ROOT=/var/www/html/public,TYPO3_PATH_APP=/var/www/html
    ddev start
    # narrow TYPO3 version to 12.4 LTS
    ddev composer require typo3/cms-core:12.4.6
    just _install-typo3 typo3
    git checkout -- composer.json

ddev-13-4: ddev-clean _ddev-dirs _add-im7
    just _ddev-config 8.3 TYPO3_CONTEXT=Development,TYPO3_PATH_ROOT=/var/www/html/public,TYPO3_PATH_APP=/var/www/html
    ddev start
    # narrow TYPO3 version to 13.4 LTS
    ddev composer require typo3/cms-core:13.4.5
    just _install-typo3 typo3
    git checkout -- composer.json

_add-im7:
    cp ddev.Dockerfile .ddev/web-build/Dockerfile

alpine-stock:
    docker build -t typo3-bugs:91274-alpine - < alpine-package.Dockerfile
    echo "Pleaas open http://localhost:8080/"
    docker run --rm -it -p 8080:8080 typo3-bugs:91274-alpine /bin/ash -c 'cd /var/www/ && touch public/FIRST_INSTALL && php -S 0.0.0.0:8080 -t public/'

alpine-compile:
    docker build -t typo3-bugs:91274-alpine-compile - < alpine-compile.Dockerfile
    echo "Pleaas open http://localhost:8080/"
    docker run --rm -it -p 8080:8080 typo3-bugs:91274-alpine-compile /bin/ash -c 'cd /var/www/ && touch public/FIRST_INSTALL && php -S 0.0.0.0:8080 -t public/'

reproduce:
    ddev typo3 bugs:init
    ddev launch
