=== Smilies Themer Toolbar ===
Contributors: frasten
Donate link: http://polpoinodroidi.com/wordpress-plugins/smilies-themer-toolbar/#donations
Tags: comments, smileys, smilies, toolbar, admin, jquery
Requires at least: 2.6.0
Tested up to: 3.1
Stable tag: 2.0.8

Adds a toolbar to easily add to comments your smilies managed by Smilies Themer plugin.

== Description ==

**IMPORTANT: this plugin has been abandoned!**

If you are a developer and would like to fork, feel free to do so (under the
GPLv3 license). No need to contact me.

Smilies Themer Toolbar provides a toolbar to easily add custom smilies to comments.

It also integrates into tinyMCE, the rich text editor that you use when
you write the posts.

It integrates with [Smilies Themer](http://wordpress.org/extend/plugins/smilies-themer/)
plugin, and therefore **requires** it.

You can change your smilies theme in your blog, and the toolbar is automagically updated!

**NEW!!!** Now you can choose which smilies to show, and even in what
order, via a easy drag and drop interface!


For further info visit [plugin homepage](http://polpoinodroidi.com/wordpress-plugins/smilies-themer-toolbar/).

== Installation ==

Smilies Themer Toolbar can be installed easily:

1. Install [Smilies Themer](http://wordpress.org/extend/plugins/smilies-themer/) plugin.
1. Extract the files in the .zip archive, and upload them (including subfolders) to your /wp-content/plugins/ directory.
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Clicking on smilies doesn't work! =

Double check if your theme calls wp_footer() in footer.php, if not, add this
near the end of the file: `<?php wp_footer(); ?>`


= Can I place the toolbar where I want? =

By default, Smilies Themer Toolbar is placed after the comments form, but
if you want to put it in a nicer position, don't worry, it isn't that difficult.

Edit your theme (comments.php) and put this code where you want to show the toolbar:

`<?php if (function_exists('sm_toolbar_show')) sm_toolbar_show();?>`


= Some smilies show as text, for example `<3` ... What can I do? =

It's an issue in your smilies package, but you can fix it quite easily.

Go inside your smilies package, and open `package-config.php` with a text
editor.

You will find plenty of things like this: `':-)' => 'smile.png'`.

Check if the smiley (the text before `=>`, between `''`) contains special
characters like `<`, `>`, `&` and replace them with `&lt;`, `&gt;` and `&amp;`.

Then save and upload the file.


= I want to translate the plugin in my language! =

Now you can easily translate the plugin online here:
https://translations.launchpad.net/wp-smilies-themer-toolbar/trunk/

I'll add your translations to the plugin!


== Screenshots ==

1. The toolbar in a default installation
2. The toolbar with a customized theme
3. You can set a custom order for your smileys in the admin page
4. If you choose to hide by default some smileys, they can be shown by
   clicking on **More**.
5. A smiley toolbar can help you when you're writing/editing a post or a page.

== Changelog ==

= 2.0.8 =
* Added Japanese translation, thanks to Chestnut.

= 2.0.7 =
* Fixed compatibility with WP 3.1.

= 2.0.6 =
* Fixed alt-text mouseover with smilies containing special html characters:
  `<`, `>`, `&`.

= 2.0.5 =
* Now the smiley text is shown on mouseover also in Firefox, Chromium,
  Opera and Safari.

= 2.0.4 =
* Fixed an issue in the options page with icon themes containing many
  smilies: they were overflowing out of the boxes.
* Added an icon to the admin menu.
* Updated italian translation.

= 2.0.3 =
* Fixed an issue with duplicate smilies in some themes (e.g. Mystique)
* Fixed compatibility with MCEComments.
* Added (very) initial Turkish translation. Thanks to zeugma.

= 2.0.2 =
* Fixed an issue with some themes (e.g. Mandingo): the smilies were all
  in a row, causing the layout to be screwed up.
* Fixed an issue with some external plugins removing newlines in javascript
  code.

= 2.0.1 =
* Critical bugfix: jQuery wasn't loaded, so if it wasn't already loaded
  by another plugin/theme, it didn't work at all.

= 2.0 =
* Completely rewritten. Hopefully I haven't introduced many bugs.
* New feature: now you can choose which smilies to show in the toolbar.
  You can still view the others clicking on *More*.
* New feature: now you can rearrange the order of the smilies, via a
  drag 'n drop interface.
* Added multilanguage support.
* Added italian translation.
* Now it's based on jQuery, it's faster, less buggy and easier to maintain.
* Dropped the popup in tinyMCE, it was ugly and a PITA to maintain. You can
  use the nice smilies box in the new post page, it's way better.

= 1.4.1 =
* Improved compatibility in admin page with WP prior to 2.7
* Another trivial change.

= 1.4 =
* New feature: Now the smilies toolbar in the admin area works also with
  the HTML editor.
* Fixed some javascript errors in the previous release.
* Now the script is loaded only when needed.

= 1.3 =
* New feature: movable smilies box in admin area, when writing a new
  post. Move it where you want (WP>=2.7)

= 1.2.1 =
* Fixed compatibility with WP 2.6 in admin page.

= 1.2 =
* Fixed compatibility with WordPress 2.6.


== Upgrade Notice ==

= 2.0.6 =
* Fixes mouseover text on smilies containing special characters.
