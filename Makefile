.PHONY: setup

setup:
	@php -r "file_exists('.env') || copy('.env.example', '.env');"
	./vendor/bin/sail up -d
	./vendor/bin/sail composer install
	./vendor/bin/sail artisan key:generate
	./vendor/bin/sail artisan migrate:refresh --seed
	./vendor/bin/sail artisan search:reindex-products

