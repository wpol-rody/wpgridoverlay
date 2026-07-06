#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$ROOT_DIR"

composer run release:check
composer run dist

echo "Release package is ready:"
echo "/Users/rody/Desktop/Rody/rodyvdkar.nl/Overlay plugin/gridly-design-overlay.zip"
