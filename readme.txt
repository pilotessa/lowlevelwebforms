=== Lowlevel Webfroms ===
Contributors: carloroosen, pilotessa
Tags: forms html webform application
Requires at least: 3.0.1
Tested up to: 3.8.1
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Build forms with html, send notification emails or use them in your application

== Description ==
[youtube http://www.youtube.com/watch?v=101Uj00kmd0]

This plugin is experimental and needs further development. Please let us know in what direction you want it to be developed.

Most webform plugins let you create forms interactively. They have many options, some even let you insert PHP code. But in the end they all create some html.

The idea of this plugin is that it creates basic html which you then can modify for your needs. The plugin sends emails to the visitor and the admin, for each of them you can create a template containing the field values. And it provides the hooks you need to create your own functionality.

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

For basic usage, see the video. In functions.php the following options are available:

hooks:
* lw_webform_form_setup
* lw_webform_form_validate
* lw_webform_form_action

global variables:
* global $lw_webform_id;
* global $lw_webform_errors;
* global $lw_webform_messages;
* global $lw_webform_values;
* global $lw_webform_search;
* global $lw_webform_replace;
* global $lw_webform_use_admin_email;
* global $lw_webform_use_user_email;
* global $lw_webform_success_page;
* global $lw_webform_success_message;
* global $lw_webform_admin_attachments;
* global $lw_webform_user_attachments;

They are used to reset values defined on the form page. Please contact us for the full description

== Changelog ==

= 0.1 =
* First commit
