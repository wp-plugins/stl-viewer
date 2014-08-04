=== Plugin Name ===
Contributors: christian.loelkes
Plugin Name: STL Viewer
Plugin URI: http://wordpress.org/extend/plugins/stl-viewer/
Description: STL Viewer for WordPress
Version: 0.6
Stable tag: 0.6
Tags: stl, 3d, viewer, shortcode, 3d printing, 3d scanning, kinect
Requires at least: 3.0
Tested up to: 3.5.1
Author: Christian Lölkes
Author URI: http://www.db4cl.com
License: GPLv2

== Description ==

With a simple shortcode you can enable and embed a javascript viewer called thingiview. This plugin is still under development and things might change alot until version 1.0.

In the current version:

* STL files have to be uploaded to /wp-content/uploads
* the viewer can't be used more than one time per page. This might be a feature because the viewer needs a lot of resources.

== Installation ==

1. Upload the directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

3. Upload an STL-File to WordPress
4. Insert the shortcode [stl file="filename.stl" /]

If no filename is specified it will look for a file with the name 'your_post_title'-web.stl. This will be changed so you can customize it.

5. You can use the following options in the shortcode. These will override the default settings.

* file: the filname in wp-content/uploads/
* width: width of the viewer. Can be in % or px.
* height: height of the viewer. Can be in % or px.
* floor: url to floor texture
* rotation: rotate the object (x,y,z) in degrees.

== Screenshots ==

1. The viewer with control buttons (outdated since the controls are currently not available).

== Changelog ==

= 0.6 =
* Moved away from the old thingiview thing. Complete rewrite of the Threejs part.
* Due to new Threejs part the controls are missing (sorry for this step back)
* Code optimizing
* New options

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

