<?php

/*
Plugin Name: STL Viewer
Plugin URI: http://wordpress.org/extend/plugins/stl-viewer/
Description: STL Viewer for WordPress
Version: 1.0
Stable tag: 1.0
Author: Christian Loelkes
Author URI: http://www.db4cl.com
License: GPL2

Copyright 2013  Christian Loelkes  (email : christian.loelkes@gmail.com)

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
require_once( sprintf( "%s/settings.php", dirname(__FILE__) ) );

if(!class_exists('STLViewer')) {
    class STLViewer {

        public $STLViewer_Settings;

        public function __construct() {
            $this->STLViewer_Settings = new STLViewer_Settings(); // Initialize the settings class

            add_shortcode( 'stl', array(&$this, 'insert_STL') );
            add_shortcode( 'webgl_test', array(&$this, 'WebGL_test') );

            register_activation_hook( 	__FILE__, array( $this, 'activate' ));
            register_deactivation_hook(	__FILE__, array( $this, 'deactivate' ));

		} // END public function __construct

        public function insert_STL( $atts ) {

            global $post; 	//This is needed to generate the filename from the postname.
            $upload_dir = wp_upload_dir();

            $shortcode_defaults = array();
            $settings = $this->STLViewer_Settings->getSettingsArray();
            foreach($settings as $setting) {
                $setting_name = $this->STLViewer_Settings->getSettingPrefix().$setting['name'];
                $shortcode_defaults[$setting_name] = get_option($setting_name);
            }
            $shortcode_defaults['file'] = $post->post_name.'-web.stl';

            extract( shortcode_atts( $shortcode_defaults, $atts ) );


            // The code for the WebGL canvas
            $threejs="<script>
                    var container = document.getElementById('canvas');

                    var SCREEN_WIDTH = container.clientWidth;
                    var SCREEN_HEIGHT = container.clientHeight;

                    file        = '".$upload_dir['baseurl']."/".$file."';
                    floor       = '".$stlviewer_floor."';

                    object_rotation_offset.set( ".$stlviewer_rotation_x." * Math.PI / 180, ".$stlviewer_rotation_z." * Math.PI / 180, ".$stlviewer_rotation_y." * Math.PI / 180, 'XZY' );
                    ambient_light_color = ".$stlviewer_ambient_light_color.";

                    fog_color   = ".$stlviewer_fog_color.";
                    fog_near    = ".$stlviewer_fog_near.";
                    fog_far     = ".$stlviewer_fog_far.";

                    floor_scale.set( ".$stlviewer_floor_scale.", 1 );
                    floor_repeat.set( ".$stlviewer_floor_repeat." );

                    point_light_intensity = ".$stlviewer_point_light_intensity.";
                    directional_light_intensity = ".$stlviewer_directional_light_intensity.";

                    point_light_color = ".$stlviewer_point_light_color.";
                    directional_light_color = ".$stlviewer_directional_light_color.";

                    function setLights() {
                        directional_light.position.set(" . $stlviewer_directional_light_position . ");
                        point_light.position.set(" . $stlviewer_point_light_position . ");
                    }

                    if ( ! Detector.webgl ) noWebGL();
                    else {
                        init('STL');
                        animate();
                    }

                    </script>";

            // The canvas where the scene will be rendered.
            $webgl_canvas ='
                    <div id="progress" style="width: 100%; text-align: center">'.  get_option('stl_div_loading_text').'</div>
                    <div id="webGLError" style="width: 100%; text-align: center">'.get_option('stl_div_webgl_error').'</div>
                    <div id="canvas" style="width:'.$stlviewer_width.';height:'.$stlviewer_height.'"></div>
                    <div id="quality_notes" style="width: 100%; text-align: center">'.get_option('stl_div_informations').'</div>';

            $controls = '
                    <button onclick="viewSide(\'front\')">Front</button>
                    <button onclick="viewSide(\'left\')">Left</button>
                    <button onclick="viewSide(\'right\')">Right</button>
                    <button onclick="viewSide(\'rear\')">Rear</button>
                    <button onclick="viewTop()">Top</button>
                    ';

            if( $stlviewer_hide_controls ) $controls = NULL;

            return $webgl_canvas.$threejs.$controls;
        } // End of insert_stl
        public function WebGL_test() {
            // The javascript
            $test_webgl="
                    <script>
                        text = document.getElementById('text');
                        if ( Detector.webgl ) {
                            console.log('WebGL is supported by your system.')
                            text.innerHTML = '".get_option('webgl_success_msg')."';
                        }
                        else {
                            console.log('WebGL is not supported by your system.')
                            text.innerHTML = '".get_option('webgl_fail_msg')."';
                        }
                    </script>";

            $text='<div id="text"></div>';
            return $text.$test_webgl;
        } // End of WebGL_test()

		public function activate() {
            $settings = $this->STLViewer_Settings->getSettingsArray();
            foreach($settings as $setting){
                add_option($this->STLViewer_Settings->getSettingPrefix().$setting['name'], $setting['default']);
            }
        }
		public function deactivate() {
            if( get_option('stlviewer_delete_settings') ) {
                $settings = $this->STLViewer_Settings->getSettingsArray();
                foreach ($settings as $setting) {
                    delete_option($this->STLViewer_Settings->getSettingPrefix() . $setting['name']);
                }
            }
        }

	} // END class STLViewer
} // END if(!class_exists('STLViewer'))

// Following functions are for upcoming versions.
function isSTL( $file_ID ) {
	$file = get_attached_file( $file_ID );
	$extension = strtolower( substr( $file, -3 ));
	if( $extension == "stl" ) return true;
	else return false;
}

function stl_img_create($file_ID) {
	$plugin_dir = plugin_dir_path( __FILE__ );
	if( isSTL($file_ID) ) mkdir( $plugin_dir.'img/stl-'.$file_ID );
}

function stl_img_delete($file_ID) {
	$plugin_dir = plugin_dir_path( __FILE__ );
	if( isSTL($file_ID) ) rmdir( $plugin_dir.'img/stl-'.$file_ID );
}

// Add the JS Scripts
function ThreeJS_Scripts() {
	wp_enqueue_script( 'ThreeJS', 		plugins_url( 'js/three.min.js' , __FILE__ ));
	wp_enqueue_script( 'STLLoader', 	plugins_url( 'js/STLLoader.js' , __FILE__ ));
	//wp_enqueue_script( 'OBJLoader', 	plugins_url( 'js/OBJLoader.js' , __FILE__ )); // For later use
	wp_enqueue_script( 'TrackballControls', plugins_url( 'js/TrackballControls.js' , __FILE__ ));
	wp_enqueue_script( 'Detector', 		plugins_url( 'js/Detector.js' , __FILE__ ));
	wp_enqueue_script( 'Viewer', 		plugins_url( 'js/STLViewer.js' , __FILE__ ));
}

function plugin_settings_link($links) { 			// Add the settings link to the plugins page
    $settings_link = '<a href="options-general.php?page=stlviewer">Settings</a>';
    array_unshift($links, $settings_link);
    return $links;
}

$stlviewer_plugin = new STLViewer(); 					// instantiate the plugin class
//add_filter( 'add_attachment', 'stl_img_create' );		// For later use
//add_filter( 'delete_attachment', 'stl_img_delete' );	// For later use

add_action( 'wp_enqueue_scripts', 'ThreeJS_Scripts' );
$plugin = plugin_basename(__FILE__);
add_filter("plugin_action_links_".$plugin, 'plugin_settings_link');




