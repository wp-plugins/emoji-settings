=== Emoji Settings ===
Contributors: Cybr
Tags: emoji, enable, disable, option, writing, emoticon, script, print, tinymce, admin, frontend, mail, filter, settings
Requires at least: 4.2.0
Tested up to: 4.2.3
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Emoji Settings adds an option in your Writing Settings page to disable or enable emojis.

== Description ==

= Emoji Settings =

**Simply enable or disable emoji's with an option**

The option can be found under `wp-admin/options-writing.php`

> <strong>Enabled by default</strong><br>
> This plugin has been written for WordPress Multisite with a WordPress.com like environment in mind.
>
> Because of this, we want to give users full functionality and awesomeness with the least configuration.
> If a user wishes to disable emojis for their site, they can simply do so in their dashboard.

= Translating =

This plugin is fully translated to Dutch. If you wish to submit a translation, please contact me at the [CyberWire contact page](https://cyberwire.nl/contact/).

== Installation ==

1. Install Emoji Settings either via the WordPress.org plugin directory, or by uploading the files to your server.
1. Either Network Activate this plugin or activate it on a single site.
1. You can now disable emoji's through the admin menu under wp-admin/options-writing.php
1. That's it! Enjoy!

== Changelog ==

= 1.0.4 =
* This plugin now supports PHP 5.2 and up

= 1.0.3 =
* Now correctly removes scripts from admin pages

= 1.0.2 =
* Fixed option call priority

= 1.0.1 =
* Fixed html in option page
* Added filter 'the_emoji_options', read "Other Notes" for more information and usage

= 1.0.0 =
* Initial Release


== Filters ==

There's only one filter for this plugin, this filter changes the default settings of Emoji Settings.

Add any of these filter functions to your theme functions.php or template file.
Or a seperate plugin.

**I couldn't get is_single, is_home, etc. checks to work with this filter, so don't bother trying that for now :) use as described below.**
**You could however add any of these filters to any of your templates to get the desired result described above. It depends however if your theme supports that, of course.**
*You could also combine the options['key'], but I don't see a reason to do so*

`//* Modify Default Emoji settings, example
add_filter( 'the_emoji_options', 'my_default_emoji_settings' );
function my_default_emoji_settings( $options ) {

	// Set this to 1 or 0 to enable or disable Emoji output by default. Great for multisite installations.
	// Default is 1.
	$options['default'] = '0';

	return $options;
}`

`//* Override the emoji setting and disable output, example
add_filter( 'the_emoji_options', 'my_disable_emoji' );
function my_disable_emoji( $options ) {
	// Set this to true to disable emoji output anyway regardless of other settings. Set to false to rely on the option in the Writing Settings page.
	// Default is false
	// Example: Disable emojis on home page regardless of settings.
	$options['disable'] = true;

	return $options;
}`

`//* Override the emoji setting and enable output, example
add_filter( 'the_emoji_options', 'my_postpage_emoji_function' );
function my_postpage_emoji_function( $options ) {
	// Set this to true to enable emoji output anyway. Set to false to rely on the option in the Writing Settings page.
	// Default is false
	// Example: Enable emoji's on Post type pages regardless of settings.
	$options['enable'] = true;

	return $options;
}`
