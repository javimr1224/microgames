#!/bin/sh

set -eu

attempt=0
max_attempts=300

until php -r '$socket = @fsockopen("127.0.0.1", 9000); if ($socket === false) { exit(1); } fclose($socket);'; do
    attempt=$((attempt + 1))

    if [ "$attempt" -ge "$max_attempts" ]; then
        echo "PHP-FPM did not become ready on 127.0.0.1:9000" >&2
        exit 1
    fi

    sleep 0.1
done

echo "PHP-FPM is ready; starting nginx"
exec nginx -g 'daemon off;'
