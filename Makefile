#
# Makefile for Chargify SDK for PHP
#

.DEFAULT_GOAL := help

.PHONY: help test testdox testcoverage

VAGRANT_BOX := crucial-web

help:
	@echo "Please use \`make <target>' where <target> is one of"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

start: ## [HOST] Start the Vagrant box
	@cd ~/Sandbox/vagrant; vagrant up $(VAGRANT_BOX)
	@$(MAKE) ssh

stop: ## [HOST] Suspend the Vagrant box
	@cd ~/Sandbox/vagrant; vagrant suspend $(VAGRANT_BOX)

ssh: ## [HOST] SSH to the Vagrant box
	@cd ~/Sandbox/vagrant; vagrant ssh $(VAGRANT_BOX) \
		--command 'cd /vagrant/chargify-sdk-php && /bin/bash'

test: ## [GUEST] Run the unit tests
	@cd tests/phpunit && ../../vendor/bin/phpunit

testdox: ## [GUEST] Run the unit tests with testdox output
	@cd tests/phpunit && ../../vendor/bin/phpunit --testdox

testcoverage: ## [GUEST] Run the unit tests and generate a code coverage report
	@cd tests/phpunit && ../../vendor/bin/phpunit --coverage-html artifacts/coverage