=== Plugin Name ===
Contributors: christian.loelkes
Plugin Name: STL Viewer
Plugin URI: http://wordpress.org/extend/plugins/stl-viewer/
Description: STL Viewer for WordPress
Version: 1.1
Stable tag: 1.1
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=VNRJ5FSUV3C6L
Tags: stl, 3d, viewer, shortcode, 3d printing, 3d scanning, kinect
Requires at least: 3.0
Tested up to: 4.1.1
Author: Christian Lölkes
Author URI: http://www.db4cl.com
License: CC Attribution-NoDerivatives 4.0 International

== Description ==

With a simple shortcode you can enable and embed a WebGL viewer to show 3d stl files.

In the current version:

* STL files have to be uploaded to /wp-content/uploads
* The viewer can't be used more than one time per page. This might be a feature because the viewer needs a lot of resources.

A complete description of the plugin can be found on [my blog](https://www.db4cl.com/projects/stlviewer-1-0-for-wordpress/).

== Installation ==

1. Upload the directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

3. Upload an STL-File to WordPress
4. Insert the shortcode [stl file="filename.stl" ]

A complete description of the plugin can be found on [my blog](https://www.db4cl.com/projects/stlviewer-1-0-for-wordpress/)

== Screenshots ==

None for the moment.

== Changelog ==

= 1.1 =
* Minor bug fixing.
* Scripts are only loaded if a shortcode was found on the page.

= 1.0 =
* Complete rewrite of the plugin.
* Many settings

= 0.5 =
* Removed hard-coded links.

= 0.4.1 =
* the settings page is working again.

= 0.4 =
* added stuff to the readme file
* cleanup

= 0.3 =
* code cleaning
* settings page is now working

= 0.2 = 
* less hard-coded stuff
* added more parameters for the shortcode
* cleaning

= 0.1 = 
* first working version based on a howto plugin from the web

