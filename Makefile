### Variables

# Applications
COMPOSER ?= /usr/bin/env composer

### Helpers
all: clean depend

.PHONY: all

### Dependencies
depend:
	${COMPOSER} install --prefer-source --no-interaction

.PHONY: depend

### QA
qa: lint phpmd phpcs phpcpd

lint:
	find ./src -name "*.php" -exec /usr/bin/env php -l {} \; | grep "Parse error" > /dev/null && exit 1 || exit 0

phploc:
	vendor/bin/phploc src

phpmd:
	vendor/bin/phpmd --suffixes php src/ text codesize,design --exclude Extension.php,Provider.php,Task.php,Taskmanager.php,RabbitMQReturnConsumerWorker.php
    ###False positive :
        #Exclude Provider (PSR11),
        #Task and RabbitMQReturnConsumerService (phpmd consider interfaces as classes....)

phpcs:
	vendor/bin/phpcs --standard=PSR2 --extensions=php src/

phpcpd:
	vendor/bin/phpcpd src/

.PHONY: qa lint phploc phpmd phpcs phpcpd

### Testing
test:
	php -dxdebug.coverage_enable=1 vendor/bin/phpunit -c phpunit.xml -v --colors --coverage-text

.PHONY: test

### Cleaning
clean:
	rm -rf vendor

.PHONY: clean
