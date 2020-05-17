lint:
	composer run-script phpcs -- --standard=PSR12 src bin
test:
	composer run-script test -- tests --whitelist tests --coverage-clover coverage-report
