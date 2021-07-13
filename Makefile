.PHONY: update
update:
	composer update

.PHONY: install
install:
	composer install --no-dev

.PHONY: install-dev
install-dev:
	composer install

.PHONY: phpcs
phpcs:
	./vendor/bin/phpcs --standard=./vendor/spryker/code-sniffer/Spryker/ruleset.xml ./src/FondOfSpryker/

.PHONY: phpcbf
phpcbf:
	./vendor/bin/phpcbf --standard=./vendor/spryker/code-sniffer/Spryker/ruleset.xml ./src/FondOfSpryker/

.PHONY: phpstan
phpstan:
	php -d memory_limit=-1 ./vendor/bin/phpstan analyse -l 4 ./src/FondOfSpryker

.PHONY: codeception
codeception:
	./vendor/bin/codecept run --env standalone --coverage --coverage-xml --coverage-html

.PHONY: phpmd
phpmd:
	./vendor/bin/phpmd ./src/FondOfSpryker xml cleancode,codesize,controversial,design --exclude Git

.PHONY: phpcpd
phpcpd:
	./vendor/bin/phpcpd ./src/FondOfSpryker

.PHONY: grumphp
grumphp: phpcs phpstan codeception phpmd phpcpd

.PHONY: test
test: install-dev phpcs phpstan codeception phpmd phpcpd

