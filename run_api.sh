#!/bin/bash
if [[ "$1" = "test" ]]; then
    export FLASK_APP=/var/growapi/test/server
    echo "Running test server"
    export FLASK_ENV=development
    FLAGS="-h 192.168.0.67 -p 5001"
else
    export FLASK_APP=/var/growapi/prod/server
    FLAGS="-h 192.168.0.67"
fi

/usr/local/bin/flask run $FLAGS
