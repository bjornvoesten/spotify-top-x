.PHONY: ci

ci:
	@docker run --rm \
         -v $$(pwd):/var/www/html \
         -w /var/www/html \
         laravelsail/php83-composer:latest \
         composer install --ignore-platform-reqs --no-scripts
