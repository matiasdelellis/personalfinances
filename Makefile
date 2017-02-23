# Makefile for building the project

app_name=ownnotes
project_dir=$(CURDIR)/../$(app_name)
build_dir=$(CURDIR)/build/artifacts
appstore_dir=$(build_dir)/appstore
source_dir=$(build_dir)/source
package_name=$(app_name)

all: dist

deps:
	wget http://builds.handlebarsjs.com.s3.amazonaws.com/handlebars-v4.0.5.js
	wget https://cdn.datatables.net/1.10.13/js/jquery.dataTables.js
	wget https://cdn.datatables.net/1.10.13/css/jquery.dataTables.css
	wget https://github.com/chartjs/Chart.js/releases/download/v2.5.0/Chart.bundle.js
	mkdir -p vendor
	mv handlebars-v4.0.5.js vendor/handlebars.js
	mv jquery.dataTables.* vendor/
	mv Chart.bundle.js vendor/Chart.js

clean:
	rm -rf $(build_dir)
	rm -rf vendor/*

dist: clean
	mkdir -p $(source_dir)
	tar cvzf $(source_dir)/$(package_name).tar.gz $(project_dir) \
	--exclude-vcs \
	--exclude=$(project_dir)/build/artifacts \
	--exclude=$(project_dir)/js/node_modules \
	--exclude=$(project_dir)/js/coverage

appstore_package: clean
	mkdir -p $(appstore_dir)
	tar cvzf $(appstore_dir)/$(package_name).tar.gz $(project_dir) \
	--exclude-vcs \
	--exclude=$(project_dir)/build \
	--exclude=$(project_dir)/js/node_modules \
	--exclude=$(project_dir)/js/.bowerrc \
	--exclude=$(project_dir)/js/.jshintrc \
	--exclude=$(project_dir)/js/Gruntfile.js \
	--exclude=$(project_dir)/js/*.json \
	--exclude=$(project_dir)/js/*.conf.js \
	--exclude=$(project_dir)/js/*.log \
	--exclude=$(project_dir)/js/README.md \
	--exclude=$(project_dir)/js/.bowerrc \
	--exclude=$(project_dir)/.travis.yml \
	--exclude=$(project_dir)/phpunit*xml \
	--exclude=$(project_dir)/Makefile \
	--exclude=$(project_dir)/tests
