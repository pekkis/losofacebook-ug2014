Losofacebook (for PHPUG 2014)
===============================

Requirements
-------------

* PHP 5.5+
* Neo4j
* node.js

Installation
--------------

The app is separated to server (php / silex) and front (react). Data is stored in neo4j.

* Install requirements. npm, grunt, composer, the usual stuff.
* Fetch el grande fake names file (too large to github) from: [http://dr-kobros.com/lib/fake-names.csv.bz2]
* configure web server. Use nginx conf or follow it. urls are hard coded.
* Put it to server/app/dev/fake-names/FakeNameGenerator.com-United_States (or something like that)

	cd server
	php app/console.php dev:init:db
	php app/console.php dev:init:images
	php app/console.php dev:create-random-losofaces
	php app/console.php dev:create-gaylord-lohiposki
	php app/console.php dev:create-posst

It may work after this. :)

License
--------

Read LICENSE file.