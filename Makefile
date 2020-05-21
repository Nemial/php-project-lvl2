install:
	composer install
lint:
	composer phpcs -- --standard=PSR12 src bin tests
test:
	composer phpunit -- tests
test-coverage:
	composer phpunit -- tests --whitelist tests --coverage-clover coverage-report
