#!/usr/bin/env bash

set -euo pipefail
set -v

PHPDOC_IMAGE_ID=$(docker build . 2>&1 >/dev/null | grep 'writing image' | cut -f 2 -d: | cut -f 1 -d ' ')

# Build phpDoc in ./docs
docker run --user="${UID}" --rm -v ".:/data" "${PHPDOC_IMAGE_ID}"

# Build README.md from phpDocs CprNumber documentation
(
	# Fix phpDoc using mixed as return type of constructor
	sed 's/\(__construct.*\): mixed/\1/' |
		# Remove heavy horizontal lines
		grep -v '\*\*\*' |
		# Remove phpDoc "generated on" timestamp
		grep -v 'Automatically generated on' |
		# Fix links to phpDoc
		sed 's/(\.\/\(.*\)\.md)/(src\/\1.php)/' |
		# Remove empty lines
		cat -s
) <docs/classes/Reload/Cpr/CprNumber.md >README.md
