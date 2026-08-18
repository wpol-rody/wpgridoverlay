#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

PLUGIN_FILE="gridly-design-overlay.php"
README_FILE="readme.txt"

HEADER_VERSION="$(grep -E '^[[:space:]]*\* Version:' "$PLUGIN_FILE" | sed -E 's/.*Version:[[:space:]]*//')"
CONSTANT_VERSION="$(grep -E "^define\\( 'WPGO_VERSION'" "$PLUGIN_FILE" | sed -E "s/.*'([^']+)'.*/\\1/")"
STABLE_TAG="$(grep -E '^Stable tag:' "$README_FILE" | sed -E 's/Stable tag:[[:space:]]*//')"

if [[ -z "$HEADER_VERSION" || -z "$CONSTANT_VERSION" || -z "$STABLE_TAG" ]]; then
	echo "Could not read one or more version values."
	exit 1
fi

if [[ "$HEADER_VERSION" != "$CONSTANT_VERSION" || "$HEADER_VERSION" != "$STABLE_TAG" ]]; then
	echo "Version mismatch:"
	echo "  Plugin header: $HEADER_VERSION"
	echo "  WPGO_VERSION:  $CONSTANT_VERSION"
	echo "  Stable tag:    $STABLE_TAG"
	exit 1
fi

if ! grep -q "^= $HEADER_VERSION =$" "$README_FILE"; then
	echo "Missing changelog section for version $HEADER_VERSION in readme.txt."
	exit 1
fi

php -l "$PLUGIN_FILE"
php -l includes/class-wpgo-settings-page.php
php -l includes/class-wpgo-overlay.php
composer run lint

echo "Release checks passed for version $HEADER_VERSION."
