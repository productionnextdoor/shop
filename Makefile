#!make
include .env

#################################################################
#                         VARIABLES                             #
#################################################################

# Docker Compose configurations
COMPOSE_FILE=docker-compose.yml
COMPOSE=docker compose -f $(COMPOSE_FILE)
EXEC=$(COMPOSE) exec -it shopware

# Colours
COLOUR_GREEN=\033[0;32m
COLOUR_BLUE=\033[0;34m
COLOUR_GREY=\033[0;37m
END_COLOUR=\033[0m

#################################################################
#                         TARGETS                               #
#################################################################

.PHONY: help
help:
	@echo -e "Usage: make $(COLOUR_GREEN)<target>$(END_COLOUR) $(COLOUR_BLUE)[ARGUMENT1=value1]$(END_COLOUR) $(COLOUR_BLUE)[ARGUMENT2=value2]$(END_COLOUR)"
	@echo ""
	@echo "Targets:"
	@echo -e "  $(COLOUR_GREEN)help$(END_COLOUR)                        Show this help message"
	@echo -e "  $(COLOUR_GREEN)start$(END_COLOUR)                       Start the development server"
	@echo -e "  $(COLOUR_GREEN)stop$(END_COLOUR)                        Stop the development server"
	@echo -e "  $(COLOUR_GREEN)build$(END_COLOUR)                       Build the storefront and administration"
	@echo -e "  $(COLOUR_GREEN)build-storefront$(END_COLOUR)            Build the storefront"
	@echo -e "  $(COLOUR_GREEN)build-administration$(END_COLOUR)        Build the administration"
	@echo -e "  $(COLOUR_GREEN)cache-clear$(END_COLOUR)                 Clear the cache"
	@echo -e "  $(COLOUR_GREEN)exec$(END_COLOUR)                        Open a bash shell in the container"
	@echo -e "  $(COLOUR_GREEN)init$(END_COLOUR)                        Initialize the project"
	@echo ""


.PHONY: is-running
is-running:
	@if [ $$(docker-compose ps -q | wc -l) -eq 0 ]; then echo -e "No containers running. Please run \033[0;32mmake start\033[0m first"; exit 1; fi

.PHONY: edit-hosts
edit-hosts:
	@echo -e "Adding $(VIRTUAL_HOST) to your hosts file if it's not already there. You may be prompted for your password."
	@grep -qw "$(VIRTUAL_HOST)" /etc/hosts || echo "127.0.0.1 $(VIRTUAL_HOST)" | sudo tee -a /etc/hosts > /dev/null

.PHONY: start
start:
	$(COMPOSE) pull
	$(COMPOSE) up -d --force-recreate

.PHONY: stop
stop:
	$(COMPOSE) down

.PHONY: build
build: is-running
	$(EXEC) bin/build-js.sh

.PHONY: build-storefront
build-storefront: is-running
	$(EXEC) bin/build-storefront.sh

.PHONY: build-administration
build-administration: is-running
	$(EXEC) bin/build-administration.sh

.PHONY: cache-clear
cache-clear: is-running
	$(EXEC) bin/console cache:clear:all

.PHONY: exec
exec: is-running
	$(EXEC) bash

.PHONY: init
init: is-running edit-hosts
	@echo -n "This will drop the database and install Shopware. Are you sure? (y/n) " && read -r choice && echo "" && if [[ ! $$choice =~ ^[Yy]$$ ]]; then exit 1; fi
	$(EXEC) mysql -u root -p$(MYSQL_ROOT_PASSWORD) -e "SET GLOBAL log_bin_trust_function_creators = 1;"
	$(EXEC) bin/console system:install --drop-database --create-database --force --shop-name=$(PROJECT_NAME) --shop-email=$(PROJECT_EMAIL) --shop-locale=de-DE --shop-currency=EUR --basic-setup
	$(EXEC) mysql -u root -p$(MYSQL_ROOT_PASSWORD) -e "SET GLOBAL log_bin_trust_function_creators = 0;"
	$(EXEC) bin/console plugin:install -c --refresh PmAbstract
	$(EXEC) bin/console plugin:activate PmAbstract
	$(EXEC) bin/console plugin:install -a -c --no-interaction PmCustomFields PmHsuTheme PmLimitBasket
	@$(MAKE) build
	@echo -e "$(COLOUR_GREEN)Shopware has been installed!$(END_COLOUR)"
	@echo -e "$(COLOUR_GREY)You can access the storefront at http://$(VIRTUAL_HOST)/$(END_COLOUR)"
	@echo -e "$(COLOUR_GREY)You can access the administration at http://$(VIRTUAL_HOST)/admin$(END_COLOUR)"
	@echo -e "$(COLOUR_GREY)Credentials: admin / shopware$(END_COLOUR)"
	@echo -e "$(COLOUR_GREEN)Shopware is ready!$(END_COLOUR)"
