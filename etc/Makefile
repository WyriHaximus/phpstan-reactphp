# Documentation QA (Profile G): Docker tool images on host; prep via `make run php utils/documentation-qa.php …`.

LINKSPECTOR_VERSION := v0.5.6
DOCUMENTATION_MOUNT := -v "`pwd`:`pwd`" -w "`pwd`"
DOCUMENTATION_SECURITY := --cap-drop=ALL --security-opt="no-new-privileges=true" --user="`id -u`:`id -g`"
IMAGE_TYPOS := andreyfomin/typos@sha256:8343befcb7c90485986ba8dfc0b8a69a9404a583b5dac14bff827fa7efe62e22
IMAGE_MARKDOWNLINT := davidanson/markdownlint-cli2:v0.17.2
IMAGE_VALE := jdkato/vale:v3.9.1
IMAGE_DEBIAN := debian:bookworm-slim

documentation-files-cache: #### Discover documentation paths (make run) into var/ci/documentation-files.lst ####
	@mkdir -p var/ci && $(DOCKER_RUN) php utils/documentation-qa.php files-cache

documentation-qa: documentation-files-cache #### Run all documentation checks (typos, markdownlint, Vale/Google, links) ##*E*##
	$(MAKE) documentation-typos documentation-markdownlint documentation-vale documentation-linkspector

documentation-typos: documentation-files-cache #### Spell-check documentation ####
	xargs -0 -r -a var/ci/documentation-files.lst docker run --rm -i $(DOCUMENTATION_SECURITY) $(DOCUMENTATION_MOUNT) $(IMAGE_TYPOS) --config etc/qa/typos.toml

documentation-markdownlint: documentation-files-cache #### Lint markdown structure ####
	xargs -0 -r -a var/ci/documentation-files.lst docker run --rm -i $(DOCUMENTATION_SECURITY) $(DOCUMENTATION_MOUNT) $(IMAGE_MARKDOWNLINT) --config etc/qa/documentation.markdownlint-cli2.yaml

documentation-vale-sync: #### Download Vale style packages ####
	docker run --rm -i $(DOCUMENTATION_SECURITY) $(DOCUMENTATION_MOUNT) $(IMAGE_VALE) sync --config=etc/qa/vale.ini

documentation-vale-vocab: documentation-vale-sync #### Install committed Vale vocab into var ####
	$(DOCKER_RUN) php utils/documentation-qa.php vale-vocab

documentation-vale: documentation-vale-vocab documentation-files-cache #### Prose lint (Google developer documentation style) ####
	xargs -0 -r -a var/ci/documentation-files.lst docker run --rm -i $(DOCUMENTATION_SECURITY) $(DOCUMENTATION_MOUNT) $(IMAGE_VALE) --config=etc/qa/vale.ini --no-global

documentation-linkspector: documentation-files-cache #### Check documentation links ####
	@test -s var/ci/documentation-files.lst || exit 0
	$(DOCKER_RUN) php utils/documentation-qa.php linkspector-binary $(LINKSPECTOR_VERSION)
	$(DOCKER_RUN) php utils/documentation-qa.php linkspector-config
	docker run --rm -i -e CI=true $(DOCUMENTATION_SECURITY) $(DOCUMENTATION_MOUNT) $(IMAGE_DEBIAN) var/linkspector/linkspector check -c var/ci/linkspector.yml -q

update-phpstan-extension: ## Update rules ##*I*##
	$(DOCKER_RUN) php utils/update.php
