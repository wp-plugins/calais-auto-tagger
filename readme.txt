=== WP Calais Auto Tagger ===
Contributors: dangrossman
Tags: tags, tagging, tagger, semantic web, semweb, semantic, suggest, suggestion, post
Requires at least: 2.3
Tested up to: 2.5
Stable tag: trunk

The plugin performs semantic analysis of your post text and suggest tags for you.

== Description ==

With the Calais Auto Tagger plugin, you'll never have to think of tags for your posts again. The plugin uses the Open Calais API to perform semantic analysis of your post text and suggest tags for you. The plugin adds a new "Get Tags" button to your post interface which retrieves the suggestions, and an "Add These Tags" button which adds the suggested tags to the post.

This plugin requires PHP 5 and the cURL library (both of which are available on most web hosts).

== Installation ==

1. Upload `calais_auto_tagger.php` and `opencalais.php` to the `/wp-contents/plugins` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Add your Open Calais API key through the 'Calais Configuration' sub-page of the 'Plugins' menu

To obtan a Calais API key:

1. Go to the Open Calais website at http://opencalais.com/
2. Click "Register" at the top of the page and create an account
3. Request an API key at http://developer.opencalais.com/apps/register

You should receive the key immediately.

== Frequently Asked Questions ==

= How do I use this plugin? =

Calais Auto Tagger adds a box to your post writing and editing screens with a "Get Tags" button. Once you've written your post, click the "Get Tags" button and Calais Auto Tagger will analyze your post content and suggest a list of tags. Click the "Add Tags" button to add them to the post's tag list.

== Screenshots ==

1. Calais Auto Tagger interface