.PHONY: test lint fix phpunit phpcs phpstan all install clean

export XDEBUG_MODE=coverage

all: lint test

lint: phpcs phpstan

fix: phpcbf

README.md: vendor src bin/generate-readme.sh
	bin/generate-readme.sh

test: phpunit

install: vendor

vendor: composer.json 
	composer install

vendor/bin/phpcbf: vendor
vendor/bin/phpcs: vendor
vendor/bin/phpstan: vendor
vendor/bin/phpunit: vendor

phpcbf: vendor/bin/phpcbf
	vendor/bin/phpcbf -s -p --colors

phpcs: vendor/bin/phpcs
	vendor/bin/phpcs -s -p --colors

phpstan: vendor/bin/phpstan
	vendor/bin/phpstan analyse

phpunit: vendor/bin/phpunit
	vendor/bin/phpunit --testdox --coverage-html=coverage --colors

clean:
	$(RM) -r .phpunit.cache coverage docs vendor
