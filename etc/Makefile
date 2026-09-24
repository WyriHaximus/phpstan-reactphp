# Documentation QA (Profile G): pinned Docker tool images on host.

DOCUMENTATION_MOUNT := -v "`pwd`:`pwd`" -w "`pwd`"
DOCUMENTATION_SECURITY := --cap-drop=ALL --security-opt="no-new-privileges=true" --user="`id -u`:`id -g`"
IMAGE_TYPOS := andreyfomin/typos@sha256:8343befcb7c90485986ba8dfc0b8a69a9404a583b5dac14bff827fa7efe62e22
IMAGE_MARKDOWNLINT := davidanson/markdownlint-cli2:v0.17.2
IMAGE_VALE := jdkato/vale:v3.23.0@sha256:d87d6355dc8992f92ec39c4c862a388e56e30302a771fd4512c02660fb25cdf3
IMAGE_LYCHEE := lycheeverse/lychee:0.24.2-alpine@sha256:2255c0b916cc8fc4193f59a4549358a6bae0f7d4ea16b5e1dd4c3b2733c35504

documentation-qa: #### Run all documentation checks (typos, markdownlint, Vale, links) ##*E*##
	$(MAKE) documentation-typos documentation-markdownlint documentation-vale documentation-links

documentation-typos: #### Spell-check documentation ####
	docker run --rm -i $(DOCUMENTATION_SECURITY) $(DOCUMENTATION_MOUNT) $(IMAGE_TYPOS) --config etc/qa/typos.toml README.md AGENTS.md CONTRIBUTING.md

documentation-markdownlint: #### Lint markdown structure ####
	docker run --rm -i $(DOCUMENTATION_SECURITY) $(DOCUMENTATION_MOUNT) $(IMAGE_MARKDOWNLINT) --config etc/qa/documentation.markdownlint-cli2.yaml

documentation-vale-sync: #### Download Vale style packages ####
	docker run --rm -i $(DOCUMENTATION_SECURITY) $(DOCUMENTATION_MOUNT) $(IMAGE_VALE) sync --config=etc/qa/vale.ini

DOCUMENTATION_VALE_FILES := README.md AGENTS.md CONTRIBUTING.md $(shell find etc/ai -name '*.md' 2>/dev/null)

documentation-vale: documentation-vale-sync #### Prose lint (write-good, inclusivity, Diataxis) ####
	docker run --rm -i $(DOCUMENTATION_SECURITY) $(DOCUMENTATION_MOUNT) $(IMAGE_VALE) --config=etc/qa/vale.ini --no-global $(DOCUMENTATION_VALE_FILES)

documentation-links: #### Check documentation links ####
	docker run --rm -i $(DOCUMENTATION_SECURITY) $(DOCUMENTATION_MOUNT) $(IMAGE_LYCHEE) --config etc/qa/lychee.toml .

update-phpstan-extension: ## Update rules ##*I*##
	$(DOCKER_RUN) php utils/update.php
