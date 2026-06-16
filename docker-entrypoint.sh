#!/bin/bash
set -eo pipefail

php /app/docker-entrypoint.php

if [[ $# -gt 0 ]] ; then
  exec "$@"
fi

exec rr serve -c .rr.yaml
