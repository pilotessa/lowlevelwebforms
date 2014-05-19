=== Lowlevel Webfroms ===
Contributors: carloroosen, pilotessa
Tags: forms html
Requires at least: 3.0.1
Tested up to: 3.8.1
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Build forms with html, send notification emails or use them in your application

== Description ==
This plugin is experimental and needs further development. Please let us know in what direction you want it to be developed.

Most webform plugins let you create forms with a few button clicks. And then they have all kinds of options to define details. Some even let you insert PHP code for maximum flexibility. In the end this all leads to some html. So why not start from html in the first place?

The idea of this plugin is that creating a form in html is much easier than learning all the options of a more advanced webform plugin. This plugin only does the more difficult or time consuming part of webform creation, namely processing the result. It sends emails to the visitor and the admin, each of them can contain the field values. Plus it  provides the WordPress hooks that you will need in functions.php to create your own processing algoritms.

= Links =

* [Author's website](http://carloroosen.com/)
* [Plugin page](http://carloroosen.com/lowlevel-webforms/)

== Installation ==

1. In your WordPress backend, go to the 'Plugins' page, click 'New' and enter 'lowlevel-webforms' in the search field. Find the plugin in the list and click 'install'.
1. Activate the plugin through the 'Plugins' menu in WordPress
1. In the plugin options, add the forms you need. They will be created as custom posts, you will find them in the webforms menu.
1. Via ftp, go to /wp-content/themes/[current theme]/webforms/[form name] and modify the file. Create any html form you like.
1. In the backend on the corresponding webform post, set the options you need for sending emails. In all templates you can add {variablename} for each variable you used in the form
1. Add  custom functionality in functions.php

On installation, a contact form will automatically be created. 


== Screenshots ==

1. Option page
2. Form template
3. Webform post settings
4. functions.php

== Changelog ==

= 0.1 =
* First commit
