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
	./vendor/bin/phpcs --standard=./vendor/spryker/code-sniffer/Spryker/ruleset.xml ./src/

.PHONY: phpcbf
phpcbf:
	./vendor/bin/phpcbf --standard=./vendor/spryker/code-sniffer/Spryker/ruleset.xml ./src/

.PHONY: phpstan
phpstan:
	./vendor/bin/phpstan analyse -l 4 ./src

.PHONY: codeception
codeception:
	./vendor/bin/codecept run --coverage --coverage-xml --coverage-html

.PHONY: phpmd
phpmd:
	./vendor/bin/phpmd ./src xml cleancode,codesize,controversial,design --exclude Git

.PHONY: phpcpd
phpcpd:
	./vendor/bin/phpcpd ./src --exclude vendor tests src/Generated

.PHONY: grumphp
grumphp: phpcs phpstan codeception phpmd phpcpd

.PHONY: test
test: install-dev phpcs phpstan codeception phpmd phpcpd

