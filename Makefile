#
# Makefile for Chargify SDK for PHP
#

.DEFAULT_GOAL := help

.PHONY: help test testdox testcoverage

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

test: ## Run the unit tests
	@cd tests/phpunit && ../../vendor/bin/phpunit

testdox: ## Run the unit tests with testdox output
	@cd tests/phpunit && ../../vendor/bin/phpunit --testdox

testcoverage: ## Run the unit tests and generate a code coverage report
	@cd tests/phpunit && ../../vendor/bin/phpunit --coverage-html artifacts/coverage