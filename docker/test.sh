#!/bin/sh
# Jalankan test di Docker untuk satu kombinasi PHP x Symfony.
#   sh docker/test.sh 8.1 6.4    # PHP 8.1 + Symfony 6.4
#   sh docker/test.sh all        # PHP 8.1 & 8.3 dengan Symfony 6.4
# Argumen tambahan diteruskan ke phpunit, mis: sh docker/test.sh 8.1 6.4 --filter Foo
#
# Package kematjaya lain bisa diambil dari folder tetangga (bukan Packagist), mis:
#   LOCAL_PACKAGES="export" sh docker/test.sh all
# Versi path repo diambil dari tag git terdekat folder tersebut, atau ditentukan
# dengan "folder@versi", mis. LOCAL_PACKAGES="base-controller-bundle@6.4.0"
#
# Constraint tambahan untuk variasi dependency, mis. Doctrine ORM 2:
#   EXTRA_REQUIRE="doctrine/orm:^2.14" sh docker/test.sh 8.1 6.4
set -eu

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
NAME="$(basename "$ROOT")"
LOCAL_PACKAGES="${LOCAL_PACKAGES:-}"
EXTRA_REQUIRE="${EXTRA_REQUIRE:-}"

run() {
    php="$1"; sf="$2"; shift 2
    image="kmj-php-lib-test:php${php}"
    echo "=== ${NAME}: PHP ${php} / Symfony ${sf}"
    docker build -q --build-arg PHP_VERSION="$php" -t "$image" "$ROOT/docker" > /dev/null
    # Source di-mount read-only lalu disalin, sehingga vendor/ tiap matrix tidak saling menimpa.
    docker run --rm \
        -v "$ROOT":/src:ro \
        -v "$ROOT/..":/libs:ro \
        -v kmj-php-lib-composer-cache:/root/.composer/cache \
        -e SYMFONY_REQUIRE="${sf}.*" \
        -e LOCAL_PACKAGES="$LOCAL_PACKAGES" \
        -e EXTRA_REQUIRE="$EXTRA_REQUIRE" \
        -e SYMFONY_DEPRECATIONS_HELPER="${SYMFONY_DEPRECATIONS_HELPER:-max[self]=0&verbose=0}" \
        "$image" sh -c '
            set -e
            tar -C /src --exclude=./vendor --exclude=./composer.lock --exclude=./.git --exclude=./var -cf - . | tar -C /app -xf -
            for p in $LOCAL_PACKAGES; do
                # versi = tag terdekat di folder tersebut (branch 6.4 terbaca sebagai 6.4.x-dev)
                ver=""
                case "$p" in *@*) ver="${p#*@}"; p="${p%@*}";; esac
                name=$(php -r "echo json_decode(file_get_contents(\"/libs/$p/composer.json\"))->name;")
                [ -n "$ver" ] || ver=$(git -C "/libs/$p" describe --tags --abbrev=0)
                composer config "repositories.$p" "{\"type\":\"path\",\"url\":\"/libs/$p\",\"options\":{\"symlink\":false,\"versions\":{\"$name\":\"$ver\"}}}"
            done
            if [ -n "$EXTRA_REQUIRE" ]; then composer require --no-update --no-interaction $EXTRA_REQUIRE; fi
            composer update --no-interaction --no-progress --prefer-dist > /tmp/composer.log 2>&1 || { cat /tmp/composer.log; exit 1; }
            composer show | grep -E "^(symfony/(http-kernel|framework-bundle|form) |doctrine/orm |kematjaya/)" || true
            vendor/bin/phpunit "$@"
        ' phpunit "$@"
}

if [ "${1:-all}" = "all" ]; then
    shift $(( $# > 0 ? 1 : 0 ))
    run 8.1 6.4 "$@"
    run 8.3 6.4 "$@"
else
    php="$1"; sf="$2"; shift 2
    run "$php" "$sf" "$@"
fi
