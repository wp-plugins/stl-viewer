<?php

/*
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

*/
require_once( sprintf( '%s/settings.php', dirname(__FILE__) ) );

if(!class_exists('STLViewer')) {
    class STLViewer {

        public $STLViewer_Settings;

        static $ADD_STLVIEWER_SCRIPTS = false;
        static $ADD_TEST_SCRIPTS = false;

        public function __construct() {
            $this->STLViewer_Settings = new STLViewer_Settings(); // Initialize the settings class

            add_shortcode( 'stl', array( &$this, 'insert_STL' ) );
            add_shortcode( 'webgl_test', array( &$this, 'WebGL_test' ) );

            register_activation_hook( 	__FILE__, array( &$this, 'activate' ));
            register_deactivation_hook(	__FILE__, array( &$this, 'deactivate' ));

            add_action( 'wp_enqueue_scripts', array( &$this, 'ThreeJS_Scripts') );
            add_action( 'wp_footer', array( &$this, 'print_Scripts'));

            //add_filter( 'add_attachment', 'stl_img_create' );		// For later use
            //add_filter( 'delete_attachment', 'stl_img_delete' );	// For later use

            $plugin = plugin_basename( __FILE__ );
            add_filter( 'plugin_action_links_'.$plugin, array( &$this, 'plugin_settings_link')) ;

		} // END public function __construct

        static function ThreeJS_Scripts() {
            wp_register_script( 'ThreeJS', 		plugins_url( 'js/three.min.js' , __FILE__ ));
            wp_register_script( 'STLLoader', 	plugins_url( 'js/STLLoader.js' , __FILE__ ));
            //wp_register_script( 'OBJLoader', 	plugins_url( 'js/OBJLoader.js' , __FILE__ )); // For later use
            wp_register_script( 'TrackballControls', plugins_url( 'js/TrackballControls.js' , __FILE__ ));
            wp_register_script( 'Detector', 	plugins_url( 'js/Detector.js' , __FILE__ ));
            wp_register_script( 'Viewer', 		plugins_url( 'js/STLViewer.js' , __FILE__ ));
        }

        static function print_Scripts() {
            if( self::$ADD_STLVIEWER_SCRIPTS ) {
                wp_print_scripts( 'ThreeJS'     );
                wp_print_scripts( 'STLLoader'   );
                //wp_print_scripts( 'OBJLoader' ); // For later use
                wp_print_scripts( 'TrackballControls');
                wp_print_scripts( 'Detector'    );
                wp_print_scripts( 'Viewer'      );
            }
            else if( self::$ADD_TEST_SCRIPTS ) {
                wp_print_scripts( 'ThreeJS'     );
                wp_print_scripts( 'Detector'    );
            }
        }

        public function insert_STL( $atts ) {

            self::$ADD_STLVIEWER_SCRIPTS = TRUE;

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

            $file_url = $upload_dir['baseurl']."/".$file;
            $floor_url = $upload_dir['baseurl']."/".$stlviewer_floor;

            // The code for the WebGL canvas
            $threejs="<script>
                    var container = document.getElementById('canvas');

                    var SCREEN_WIDTH = container.clientWidth;
                    var SCREEN_HEIGHT = container.clientHeight;

                    var file        = '".$file_url."';
                    var floor       = '".$floor_url."';

                    var object_rotation_offset;

                    function setRotationOffset() {
                        object_rotation_offset = new THREE.Euler(" . $stlviewer_rotation_x . " * Math.PI / 180, " . $stlviewer_rotation_z . " * Math.PI / 180, " . $stlviewer_rotation_y . " * Math.PI / 180, 'XZY');
                    }

                    var ambient_light_color = ".$stlviewer_ambient_light_color.";

                    var fog_color   = ".$stlviewer_fog_color.";
                    var fog_near    = ".$stlviewer_fog_near.";
                    var fog_far     = ".$stlviewer_fog_far.";

                    function setFloor() {
                        floor_scale.set( " . $stlviewer_floor_scale . ", 1 );
                        floor_repeat.set( " . $stlviewer_floor_repeat . " );
                    }

                    var point_light_intensity = ".$stlviewer_point_light_intensity.";
                    var directional_light_intensity = ".$stlviewer_directional_light_intensity.";

                    var point_light_color = ".$stlviewer_point_light_color.";
                    var directional_light_color = ".$stlviewer_directional_light_color.";

                    function setLights() {
                        directional_light.position.set(" . $stlviewer_directional_light_position . ");
                        point_light.position.set(" . $stlviewer_point_light_position . ");
                    }

                    </script>";

            // The canvas where the scene will be rendered.
            $webgl_canvas ='
                    <div id="progress" style="width: 100%; text-align: center">'.$stlviewer_loading_text.'</div>
                    <div id="webGLError" style="width: 100%; text-align: center">'.$stlviewer_webgl_error.'</div>
                    <div id="canvas" style="width:'.$stlviewer_width.';height:'.$stlviewer_height.'"></div>
                    <div id="quality_notes" style="width: 100%; text-align: center">'.$stlviewer_informations.'</div>';

            $controls = '
                    <button onclick="viewSide(\'front\')">Front</button>
                    <button onclick="viewSide(\'left\')">Left</button>
                    <button onclick="viewSide(\'right\')">Right</button>
                    <button onclick="viewSide(\'rear\')">Rear</button>
                    <button onclick="viewTop()">Top</button><br />
                    ';
            $download = '<form action="'.$file_url.'"><input type="submit" class="button" value="Download this file"></form>';

            if( $stlviewer_hide_controls ) $controls = NULL;
            if( !$stlviewer_download_link ) $download = NULL;


            return $webgl_canvas.$threejs.$controls.$download;
        } // End of insert_stl
        public function WebGL_test() {
            self::$ADD_TEST_SCRIPTS = TRUE;

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

        public function plugin_settings_link($links) { 			// Add the settings link to the plugins page
            $settings_link = '<a href="options-general.php?page=stlviewer">Settings</a>';
            array_unshift($links, $settings_link);
            return $links;
        }

        public function stl_img_create($file_ID) {
            $plugin_dir = plugin_dir_path( __FILE__ );
            if( isSTL($file_ID) ) mkdir( $plugin_dir.'img/stl-'.$file_ID );
        } // Later use
        public function stl_img_delete($file_ID) {
            $plugin_dir = plugin_dir_path( __FILE__ );
            if( isSTL($file_ID) ) rmdir( $plugin_dir.'img/stl-'.$file_ID );
        } // Later use

	} // END class STLViewer
} // END if(!class_exists('STLViewer'))

// Following functions are for upcoming versions.
function isSTL( $file_ID ) {
	$file = get_attached_file( $file_ID );
	$extension = strtolower( substr( $file, -3 ));
	if( $extension == 'stl' ) return true;
	else return false;
}

$stlviewer_plugin = new STLViewer(); 					// instantiate the plugin class





