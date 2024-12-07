.PHONY: test lint fix docs phpunit phpcs phpstan all install clean

export XDEBUG_MODE=coverage

all: lint test

lint: phpcs phpstan

fix: phpcbf

docs: src vendor
	docker run --user=$(shell id -u) --rm -v ".:/data" "phpdoc/phpdoc:3"

README.md: src docs
	sed 's/\(__construct.*\): mixed/\1/' < docs/classes/Reload/Cpr/CprNumber.md | grep -v '\*\*\*' | grep -v 'Automatically generated on' | sed 's/(\.\/\(.*\)\.md)/(src\/\1.php)/' | cat -s > README.md

test: phpunit

install: vendor

vendor: composer.json 
	composer install

vendor/bin/phpcbf: vendor
vendor/bin/phpcs: vendor
vendor/bin/phpstan: vendor
vendor/bin/phpunit: vendor

phpcbf: vendor/bin/phpcbf
	-vendor/bin/phpcbf -s -p --colors

phpcs: vendor/bin/phpcs
	-vendor/bin/phpcs -s -p --colors

phpstan: vendor/bin/phpstan
	-vendor/bin/phpstan analyse

phpunit: vendor/bin/phpunit
	-vendor/bin/phpunit --testdox --coverage-html=coverage --colors

clean:
	$(RM) -r .phpunit.cache coverage docs vendor
