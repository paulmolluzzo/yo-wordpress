=== Yo ===
Contributors: paulmolluzzo
Donate link: 
Tags: yo
Requires at least: 3.0.1
Tested up to: 3.9.1
Stable tag: 1.3.1
License: MIT
License URI: http://opensource.org/licenses/MIT

This plugin will track your Yo Subscribers and allow you to send a Yo to all your subscribers when you publish a new Post.

== Description ==

This is an integration of the Yo service for Wordpress. It will track Yo subscribers and allow you to send a Yo when you make a new Post. After it's installed, go to the 'Yo' tab under Plugins. There you can enter your API Key to send Yos, and see a list of Yo subscribers, how many times they've sent you a Yo, and the last time they sent you a Yo.

= How to Use =

1. Grab a [Yo API Key](http://api.justyo.co) and make the callback URL your Wordpress home page URL.
1. Go to the Yo Worpress page in the Plugins menu.
1. Enter your Yo API Key and click "Save Yo API Key".

Now when you write some posts you'll send a Yo to all your subscribers. As you gain subscribers, they'll get listed in the table on the Yo Wordpress admin page.

== Installation ==

If you're installing via the usual WordPress installer, you click "install."

If you're manually installing:

1. Create a directory in `/wp-content/plugins/` called `yo`
1. Upload `yo.php` to the new `/wp-content/plugins/yo` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Where can I get an API Key =

[http://api.justyo.co](http://api.justyo.co)

= No subscribers are listed. What gives? =

Did you set your homepage as the callback URL with Yo? If not, send them an email at api@justyo.co

== Screenshots ==

1. The Yo page.

== Changelog ==

= 1.3.1 =

Fixed register settings name for API Key.

= 1.3 =

Yos now attach the link of the post.

= 1.2 =

Yos only sent on publishing a new, non-private post.

= 1.1 =

Fix for breaking query_vars error

= 1.0 =

Initial Release

== Upgrade Notice ==

= 1.3.1 =

Fixed register settings name for API Key.

= 1.3 =

Yos now attach the link of the post.

= 1.2 =

Yos only sent on publishing a new, non-private post.

= 1.1 =

This is a fix for a breaking error in v1.0. You must upgrade for this plugin to continue to work properly.

= 1.0 =

Initial Release