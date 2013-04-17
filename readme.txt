=== Plugin Name ===

Plugin Name: STL Viewer
Plugin URI: http://wordpress.org/extend/plugins/stl-viewer/
Description: STL Viewer for WordPress
Version: 0.4
Author: Christian Lölkes
Author URI: http://www.db4cl.com
License: GPLv2

== Description ==

With a simple shortcode you can enable and embed a javascript viewer called thingiview. This plugin is still under development and things might change alot until version 1.0.

In the current version:
* STL files have to be uploaded to /wp-content/uploads
* the viewer can't be used more than one time per page. This might be a feature because the viewer needs a lot of resources.

This plugin uses thingiview.

== Installation ==

1. Upload the directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

3. Upload an STL-File to WordPress
4. Insert the shortcode [stl file="filename.stl" /]

== Changelog ==

= 0.4 =
* added stuff to the readme file

= 0.3 =
* code cleaning
* settings page is now working

= 0.2 = 
* less hard-coded stuff
* added more parameters for the shortcode
* cleaning

= 0.1 = 
* first working version based on a howto plugin from the web

