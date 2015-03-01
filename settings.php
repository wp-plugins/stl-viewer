<?php
if(!class_exists('STLViewer_Settings'))
{
	class STLViewer_Settings
	{
		/**
		 * Construct the plugin object
		 */
		public function __construct()
		{
		// register actions
            		add_action('admin_init', array(&$this, 'render_settings'));
        		add_action('admin_menu', array(&$this, 'add_menu'));
		} // END public function __construct

        /**
         * hook into WP's admin_init action hook
         */
        public function admin_init()
        {
        	// register your plugin's settings
        	register_setting('stlviewer_settings-group', 'stl_div_height');
        	register_setting('stlviewer_settings-group', 'stl_div_width');
        	register_setting('stlviewer_settings-group', 'stl_color');
        	register_setting('stlviewer_settings-group', 'stl_background');
		register_setting('stlviewer_settings-group', 'stl_show_controls');

        	// add your settings section
        	add_settings_section(
        	    'stlviewer-section', 
        	    'STL Viewer Settings', 
        	    array(&$this, 'settings_section_stlviewer'), 
        	    'stlviewer'
        	);
        	
        	// add your setting's fields
            add_settings_field(
                'stlviewer-setting_height', 
                'Height', 
                array(&$this, 'settings_field_input_text'), 
                'stlviewer', 
                'stlviewer-section',
                array(
                    'field' => 'stl_div_height'
                )
            );
            add_settings_field(
                'stlviewer-setting_width', 
                'Width', 
                array(&$this, 'settings_field_input_text'), 
                'stlviewer', 
                'stlviewer-section',
                array(
                    'field' => 'stl_div_width'
                )
            );
            add_settings_field(
                'stlviewer-setting_color', 
                'Object color', 
                array(&$this, 'settings_field_input_text'), 
                'stlviewer', 
                'stlviewer-section',
                array(
                    'field' => 'stl_color'
                )
            );
            add_settings_field(
                'stlviewer-setting_background', 
                'Background color', 
                array(&$this, 'settings_field_input_text'), 
                'stlviewer', 
                'stlviewer-section',
                array(
                    'field' => 'stl_background'
                )
            );
            add_settings_field(
                'stlviewer-setting_controls', 
                'Show controls', 
                array(&$this, 'settings_field_checkbox'), 
                'stlviewer', 
                'stlviewer-section',
                array(
                    'field' => 'stl_show_controls'
                )
            );
            // Possibly do additional admin_init tasks
        } // END public static function activate
        
        public function settings_section_stlviewer()
        {
            // Think of this as help text for the section.
            echo 'These settings do things for the WP Plugin Template.';
        }
        
        /**
         * This function provides text inputs for settings fields
         */
        public function settings_field_input_text($args)
        {
            // Get the field name from the $args array
            $field = $args['field'];
            // Get the value of this setting
            $value = get_option($field);
            // echo a proper input type="text"
            echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value);
        } // END public function settings_field_input_text($args)

        public function settings_field_checkbox($args)
        {
            // Get the field name from the $args array
            $field = $args['field'];
            // Get the value of this setting
            $value = get_option($field);
	    if (!empty($value)) $checked = 'checked';
	    else $value = 'true';
            // do something
            echo sprintf('<input type="checkbox" name="%s" id="%s" value="%s" %s/>', $field, $field, $value, $checked);
        } // END public function settings_field_checkbox($args)
        
        /**
         * add a menu
         */		
        public function add_menu()
        {
            // Add a page to manage this plugin's settings
        	add_options_page(
        	    'STL Viewer Settings', 
        	    'STL Viewer', 
        	    'manage_options', 
        	    'stlviewer', 
        	    array(&$this, 'plugin_settings_page')
        	);
        } // END public function add_menu()
    
        /**
         * Menu Callback
         */		
        public function plugin_settings_page()
        {
        	if(!current_user_can('manage_options'))
        	{
        		wp_die(__('You do not have sufficient permissions to access this page.'));
        	}

        	// Render the settings template
        	include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
        } // END public function plugin_settings_page()
    } // END class STLViewer_Settings
} // END if(!class_exists('STLViewer_Settings'))
