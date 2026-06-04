#!/usr/bin/env bash
# Remove a mistaken `scp D:\cms\* ~/public_html/` upload from shared hosting.
# Run ON THE SERVER after reviewing the list (not on Windows).
set -euo pipefail

WEB_ROOT="${1:-$HOME/public_html}"

if [[ ! -d "$WEB_ROOT" ]]; then
    echo "Directory not found: $WEB_ROOT" >&2
    exit 1
fi

cd "$WEB_ROOT"

echo "Web root: $(pwd)"
echo ""
echo "Will remove these CMS/Laravel paths if they exist:"
items=(
    README.md
    artisan
    composer.json
    composer.lock
    package.json
    package-lock.json
    phpunit.xml
    vite.config.js
    docker-compose.octane.yml
    rr.octane.yaml
    .rr.yaml
    app
    bootstrap
    config
    database
    resources
    routes
    storage
    vendor
    node_modules
    tests
    scripts
    deploy
    public
)

for name in "${items[@]}"; do
    [[ -e "$name" ]] && echo "  - $name"
done

echo ""
read -r -p "Delete the items above? [y/N] " confirm
if [[ "${confirm,,}" != "y" ]]; then
    echo "Aborted."
    exit 0
fi

for name in "${items[@]}"; do
    if [[ -e "$name" ]]; then
        rm -rf "$name"
        echo "Removed: $name"
    fi
done

echo ""
echo "Done. Remaining files:"
ls -la
