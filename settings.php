<?php
if(!class_exists('STLViewer_Settings')) {

	class STLViewer_Settings { 							// Construct the plugin object

		public function __construct() {

			// register actions
            	add_action('admin_init', array(&$this, 'render_settings'));
        		add_action('admin_menu', array(&$this, 'add_menu'));

		} // END public function __construct

        public function admin_init() { 							// hook into WP's admin_init action hook

		////////////////////
		// Settings block // 
		////////////////////

		$sections = array(
			array('name' => 'general', 		'title' =>'STL viewer settings'),
			array('name' => 'render', 		'title' =>'WebGL render settings'),
			array('name' => 'webgl_test', 	'title' =>'WebGL tester settings')
		);		
		$settings = array(
			array('name' => 'height', 				'title' => 'Height (height) ', 		'type' => 'text',		'section' => 'general'),
			array('name' => 'width', 				'title' => 'Width (width)', 		'type' => 'text',		'section' => 'general'),
			array('name' => 'stl_div_webgl_error', 	'title' => 'WebGL error message', 	'type' => 'textarea',	'section' => 'general'),
			array('name' => 'stl_div_informations', 'title' => 'Informations', 			'type' => 'textarea',	'section' => 'general'),
			array('name' => 'stl_div_loading_text', 'title' => 'Loading text', 			'type' => 'textarea',	'section' => 'general'),
			array('name' => 'floor', 				'title' => 'Floor texture (floor)', 'type' => 'text',		'section' => 'render'),
			array('name' => 'rotation', 			'title' => 'Rotate object (rotation)', 	'type' => 'text',		'section' => 'render'),
			array('name' => 'webgl_test_success', 	'title' => 'Success message', 		'type' => 'textarea',	'section' => 'webgl_test'),
			array('name' => 'webgl_test_fail', 		'title' => 'Fail message', 			'type' => 'textarea',	'section' => 'webgl_test'),
		);

		foreach( $settings as $field) {
			register_setting('settings-group', $field['name']);
			add_settings_field($field['name'], $field['title'], array(&$this, $field['type']), 'stlviewer', $field['section'], array('field' => $field['name']));
		}
		
		foreach( $sections as $section ) {
        		add_settings_section( $section['name'], $section['title'], array(&$this, $section['name'].'_helptext'), 'stlviewer');
		}

        } // END public static function activate
        
        public function general_helptext() { echo 'These settings do things for the WP Plugin Template.'; }
        public function webgl_test_helptext() { echo 'If you insert the shortcode [webgl_test] a WebGL test is run and will print the success- or fail-message.'; }
        public function render_helptext() { echo 'How the model will be rendered.'; }
        
        public function text($args) { 											// This function provides text inputs for settings fields

            $field = $args['field']; 											// Get the field name from the $args array
            $value = get_option($field); 										// Get the value of this setting
            echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value); 		// The input field

        } 														// END public function settings_field_input_text($args)

        public function textarea($args) { 										// This function provides textarea inputs for settings fields

            $field = $args['field']; 											// Get the field name from the $args array
            $value = get_option($field); 										// Get the value of this setting
            echo sprintf('<textarea name="%s" id="%s" cols="50" rows="5">%s</textarea>', $field, $field, $value);  	// The textarea tag

        } 														// END public function settings_field_input_textarea($args)

        public function checkbox($args) {										// This function provides checkbox inputs for settings fields
            
            $field = $args['field']; 											// Get the field name from the $args array
            $value = get_option($field); 										// Get the value of this setting

	    if (!empty($value)) $checked = 'checked';
	    else $value = 'true';

            echo sprintf('<input type="checkbox" name="%s" id="%s" value="%s" %s/>', $field, $field, $value, $checked);	// The checkbox tag

        } 														// END public function settings_field_checkbox($args)
        
        public function add_menu() { 											// Add a page to manage this plugin's settings

        	add_options_page('STL Viewer Settings', 'STL Viewer', 'manage_options', 'stlviewer', array(&$this, 'plugin_settings_page'));

        } // END public function add_menu()
    
        
        public function plugin_settings_page() { 									// Menu Callback

        	if(!current_user_can('manage_options')) wp_die(__('You do not have sufficient permissions to access this page.'));
        	include(sprintf("%s/templates/settings.php", dirname(__FILE__))); 					// Render the settings template

        } // END public function plugin_settings_page()

    } // END class STLViewer_Settings

} // END if(!class_exists('STLViewer_Settings'))