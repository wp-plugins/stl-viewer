<?php
/*
Plugin Name: STL Viewer
Plugin URI: https://www.db4cl.com
Description: A STL Viewer
Version: 0.3
Author: Christian Lölkes
Author URI: http://www.db4cl.com
License: GPL2

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if(!class_exists('STLViewer'))
{
	class STLViewer
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
        	// Initialize Settings
            	require_once(sprintf("%s/settings.php", dirname(__FILE__)));
		$STLViewer_Settings = new STLViewer_Settings();

		// [bartag foo="foo-value"]
		function insert_stl( $atts ) {
			extract( shortcode_atts( array(
				'file' => 'default.stl',
				'name' => 'default',
				'color' => get_option('stl_color'),
				'background' => get_option('stl_background'),
				'width' => get_option('stl_div_width'),
				'height' => get_option('stl_div_height'),
				'show_controls' => get_option('stl_show_controls'),
			), $atts ) );

			// Include the javascript stuff.
			$javascript_includes=
				sprintf('<script type="text/javascript" src="%sjs/Three.js"></script>', plugin_dir_url(__FILE__)).
				sprintf('<script type="text/javascript" src="%sjs/plane.js"></script>', plugin_dir_url(__FILE__)).
				sprintf('<script type="text/javascript" src="%sjs/thingiview.js"></script>', plugin_dir_url(__FILE__));
			
			$upload_dir_array = wp_upload_dir();
			// This is the script for the viewer parameters.
			$thingiview='
				<script>
     					window.onload = function() {
       						thingiurlbase = "http://www.db4cl.com/wp-includes/js/thingiview";
        					thingiview = new Thingiview("'.$name.'");
        					thingiview.loadSTL("'.$upload_dir_array[baseurl].'/'.$file.'");
        					thingiview.setObjectColor(\''.$color.'\');
        					thingiview.setBackgroundColor(\''.$background.'\');
        					thingiview.initScene();
      					}
				</script>
				<div id="'.$name.'" style="width:'.$width.';height:'.$height.'"></div>';

			// Include the controls for the viewer
			$controls='
				<p>
  					<input class="btn btn-small" onclick="thingiview.setCameraView(\'top\');" type="button" value="Top" /> 
  					<input class="btn btn-small" onclick="thingiview.setCameraView(\'side\');" type="button" value="Side" /> 
  					<input class="btn btn-small" onclick="thingiview.setCameraView(\'bottom\');" type="button" value="Bottom" /> 
  					<input class="btn btn-small" onclick="thingiview.setCameraView(\'diagonal\');" type="button" value="Diagonal" /> 
  					<input class="btn btn-small" onclick="thingiview.setCameraZoom(5);" type="button" value="Zoom +" /> 
  					<input class="btn btn-small" onclick="thingiview.setCameraZoom(-5);" type="button" value="Zoom -" /> 
  					Rotation: <input class="btn btn-small" onclick="thingiview.setRotation(true);" type="button" value="on" /> | <input class="btn btn-small" onclick="thingiview.setRotation(false);" type="button" value="off" />
				</p>';

			//return "foo = {$foo}";
			if ($show_controls == 'true') return $javascript_includes.$thingiview.$controls;
			else return $javascript_includes.$thingiview;
		}
		add_shortcode( 'stl', 'insert_stl' );

        	// Register custom post types
           	// require_once(sprintf("%s/post-types/post_type_template.php", dirname(__FILE__)));
           	// $Post_Type_Template = new Post_Type_Template();
		} // END public function __construct
		/**
		 * Activate the plugin
		 */
		public static function activate()
		{
		  // Do nothing
		} // END public static function activate

		/**
		 * Deactivate the plugin
		 */
		public static function deactivate()
		{
			// Do nothing
		} // END public static function deactivate
	} // END class STLViewer
} // END if(!class_exists('STLViewer'))

if(class_exists('STLViewer')) {
	// Installation and uninstallation hooks
	register_activation_hook(__FILE__, array('STLViewer', 'activate'));
	register_deactivation_hook(__FILE__, array('STLViewer', 'deactivate'));

	// instantiate the plugin class
	$stlviewer_plugin = new STLViewer();

    // Add a link to the settings page onto the plugin page
    if(isset($stlviewer_plugin))
    {
        // Add the settings link to the plugins page
        function plugin_settings_link($links)
        {
            $settings_link = '<a href="options-general.php?page=stlviewer">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        $plugin = plugin_basename(__FILE__);
        // add_filter("plugin_action_links_$plugin", 'plugin_settings_link');
    }
}