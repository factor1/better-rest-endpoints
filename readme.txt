
=== Better Rest Endpoints ===
Author URI: https://ericwstout.com
Plugin URI: https://github.com/factor1/better-rest-endpoints
Contributors: erwstout
Tags: rest, api, endpoints, acf, json
Requires at least: 4.7.1
Tested up to: 4.9.9
Stable Tag: 1.5.2
License: GNU Version 3 or Any Later Version

A WordPress plugin that serves up slimmer WP Rest API endpoints.

== Description ==

A WordPress plugin that serves up slimmer WP Rest API endpoints, with some great
enhancements like ACF fields, WordPress menu endpoints, and more. You can
view the full documentation and features [here](https://github.com/factor1/better-rest-endpoints).

== Installation ==

1. Activate the plugin
2. Use the better-rest-endpoint API endpoint to access your data

== Frequently Asked Questions ==

= Where can I find all the documentation? =

You can find the complete documentation on [GitHub](https://github.com/factor1/better-rest-endpoints).

= Why make this plugin? =

The core Rest API is full of information we don't really need in our responses.
This was a solution to serve up exactly what we need for making beautiful sites
with great speeds.

= Do I just turn this on and it works? =

It's worth noting that Better Rest Endpoints is for the more seasoned theme or app developer.
Just activating the plugin will do nothing - you need to build/adjust your theme or
apps endpoints to use Better Rest Endpoints.

== Changelog ==

= 1.5.2, Febuary 12, 2019
* Update: Plugin version to fix SVN issue

= 1.5.1, Febuary 12, 2019
* Update: Plugin version to fix SVN issue

= 1.5.0, Febuary 11, 2019
* Add: Functionality to get menus by location

= 1.4.1, January 23, 2019 =
* Add: Functionality to get a page by slug or path for hierarchical pages
* Add: 'modified date' to all endpoints with 'date'
* Add: Support for ACF options pages
* Fix: Empty page array

= 1.3.0, June 5, 2018 =
* Add: Yoast values to responses, see docs for more information
* Update: How slug values are populated in responses

= 1.2.1, June 4, 2018 =
* Add: Permalinks to all post/page endpoints

= 1.2.0, Febuary 7, 2018 =
* Add: ACF query in endpoint url to hide acf values from the response where applicable (all collections)
* Add: Media query in endpoint url to hide featured media from the response where applicable (all collections)

= 1.1.2, January 25, 2018 =
* Fix: issue where get post by slug was returning just the first post
* Fix: instance of lefover $bwe variable naming

= 1.1.1, January 25, 2018 =
* Update: update plugin version to retrigger build.

= 1.1.0, January 25, 2018 =
* Add: get post by slug endpoint

= 1.0.2, January 19, 2018 =
* Fix: static instance warning
* Fix: failure of ACF function by including admin plugin.php
* Update: all functions named bwe_ to bre_

= 1.0.1, January 19, 2018 =
* Prep: Update files for release on WP Repo.
