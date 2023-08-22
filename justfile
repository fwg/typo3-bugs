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
      --admin-password="Admin%123" \
      --site-setup-type=site \
      --site-base-url=https://typo3-bugs.ddev.site/ \
      --site-name="Bug #81099"

_ddev-config php-version env:
    ddev config \
      --php-version={{php-version}} \
      --project-type=typo3 \
      --project-name=typo3-bugs \
      --docroot=public \
      --web-environment="{{env}}"

ddev-10-4: ddev-clean _ddev-dirs
    just _ddev-config 7.4 TYPO3_CONTEXT=Development
    ddev start
    # narrow TYPO3 version to 10.4 LTS
    ddev composer require typo3/cms-core:10.4.37
    just _install-typo3 typo3cms
    git checkout -- composer.json

ddev-11-5: ddev-clean _ddev-dirs
    just _ddev-config 8.2 TYPO3_CONTEXT=Development
    ddev start
    # narrow TYPO3 version to 11.5 LTS
    ddev composer require typo3/cms-core:11.5.30
    just _install-typo3 typo3cms
    git checkout -- composer.json

ddev-12-4: ddev-clean _ddev-dirs
    just _ddev-config 8.2 TYPO3_CONTEXT=Development,TYPO3_PATH_ROOT=/var/www/html/public,TYPO3_PATH_APP=/var/www/html
    ddev start
    # narrow TYPO3 version to 12.4 LTS
    ddev composer require typo3/cms-core:12.4.5
    just _install-typo3 typo3
    git checkout -- composer.json

reproduce:
    ddev launch
