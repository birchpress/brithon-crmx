#!/bin/bash

function bundle_path() {
    find "$1" -type f -name "index.js" -print 2> /dev/null | while read indexjs; do
        target="$(dirname $indexjs)"

        echo "bundling $indexjs ..."
        browserify $indexjs -o $target/index.bundle.js
    done
}

function bundle_all() {
    for m in $(ls -d modules/*/); do
        bundle_path "${m}assets/js/apps"
    done
}

if [ $# -eq 0 ]; then
    bundle_all
else
    if [ -d "$1" ]; then
        bundle_path $1
    else
        echo "Not found dirctory: $1"
    fi
fi
