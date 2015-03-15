<?php
if(!class_exists('STLViewer_Settings')) {
    class STLViewer_Settings {

        // General settings for the settings page / class
        const PAGE_TITLE = 'STL Viewer Settings';// Page title
        const MENU_TITLE = 'STL Viewer';         // Title in the menu
        const CAPABILITY = 'manage_options';     // Who is allowed to change settings
        const MENU_SLUG = 'stlviewer';           // Also the page name
        const SETTINGS_PREFIX = 'stlviewer_';

        // Add the tabs for the settings page here
        // For each tab you need to create a callback function
        // public function init_settings_tab_tabname() { $this->init_settings('tabname'); }
        //

        private $tabs = array(
            'default'       => '<span class="dashicons dashicons-admin-settings"></span> General Settings',
            'render'        => '<span class="dashicons dashicons-desktop"></span> Rendering options',
            'lights'        => '<span class="dashicons dashicons-lightbulb"></span> Lightning options',
            'webgl_test'    => '<span class="dashicons dashicons-admin-generic"></span> WebGL Test options',
            'help'          => '<span class="dashicons dashicons-welcome-learn-more"></span> Help',
        );

        // Empty array('name' => '', 'title' => '', 'tab' => ''),
        // Don't forget to specify a helptext, even if it's empty.
        private $sections = array(
            array('name' => 'general', 		'title' =>'General settings',           'tab' => 'default'),
            array('name' => 'render', 		'title' =>'WebGL renderer settings',    'tab' => 'render'),
            array('name' => 'default_rot', 	'title' =>'Default Object rotation',    'tab' => 'render'),
            array('name' => 'webgl_test', 	'title' =>'WebGL tester settings',      'tab' => 'webgl_test'),
            array('name' => 'fog', 	        'title' =>'Fog settings',               'tab' => 'render'),
            array('name' => 'alight', 	    'title' =>'Ambient light settings',     'tab' => 'lights'),
            array('name' => 'plight', 	    'title' =>'Pointlight settings',        'tab' => 'lights'),
            array('name' => 'dlight', 	    'title' =>'Directional light settings', 'tab' => 'lights'),
        );

        // Empty array('name' => '', 'default' => '', 'title' => '', 'type' => '', 'section' => ''),
        private $settings = array(
            array('name' => 'height', 				'default' => '600px',       'title' => 'Height (height)', 		    'type' => 'text',		'section' => 'general'),
            array('name' => 'width', 				'default' => '100%',        'title' => 'Width (width)', 		    'type' => 'text',		'section' => 'general'),
            array('name' => 'webgl_error', 	        'default' => 'WebGL Error', 'title' => 'WebGL error message', 	    'type' => 'textarea',	'section' => 'general'),
            array('name' => 'download_link',        'default' => '',            'title' => 'Show download link',        'type' => 'checkbox',	'section' => 'general'),
            array('name' => 'informations',         'default' => '',            'title' => 'Informations', 			    'type' => 'textarea',	'section' => 'general'),
            array('name' => 'loading_text',         'default' => '',            'title' => 'Loading text', 			    'type' => 'textarea',	'section' => 'general'),
            array('name' => 'delete_settings',      'default' => '',            'title' => 'Delete settings if plugin is deactivated / updated',    'type' => 'checkbox',	'section' => 'general'),

            array('name' => 'floor', 			    'default' => '',    'title' => 'Floor texture file / URL (floor)',     'type' => 'text',		'section' => 'render'),
            array('name' => 'floor_repeat', 	    'default' => '0, 0',   'title' => 'Repeat texture (x, y)',        'type' => 'text',		'section' => 'render'),
            array('name' => 'floor_scale', 	        'default' => '1, 1',   'title' => 'Scale texture (x, y)',         'type' => 'text',		'section' => 'render'),

            array('name' => 'hide_controls', 	    'default' => '', 'title' => 'Hide controls',            'type' => 'checkbox',	'section' => 'render'),
            //array('name' => 'autorotate', 			'default' => '', 'title' => 'Enable autorotation',      'type' => 'checkbox',	'section' => 'render'),

            array('name' => 'fog_color',    'default' => '0xd9dee5',        'title' => 'Fog color (hex)',     'type' => 'text',       'section' => 'fog'),
            array('name' => 'fog_near',     'default' => '1',       'title' => 'Min. fog distance',   'type' => 'text',       'section' => 'fog'),
            array('name' => 'fog_far',      'default' => '10000',   'title' => 'Max. fog distance',   'type' => 'text',       'section' => 'fog'),

            array('name' => 'ambient_light_color',          'default' => '0x202020',    'title' => 'Ambient light color (hex)',             'type' => 'text',       'section' => 'alight'),
            array('name' => 'point_light_intensity',        'default' => '0.7',         'title' => 'Point light intensity (0..1)',          'type' => 'text',       'section' => 'plight'),
            array('name' => 'point_light_color',            'default' => '0xffffff',    'title' => 'Point light color (hex)',          'type' => 'text',       'section' => 'plight'),
            array('name' => 'point_light_position',       'default' => '0, 2*dimensions.y, 1.1*dimensions.z',         'title' => 'Point light position (x, y, z)',          'type' => 'text',       'section' => 'plight'),

            array('name' => 'directional_light_intensity',  'default' => '0.7',         'title' => 'Directional light intensity (0..1)',    'type' => 'text',       'section' => 'dlight'),
            array('name' => 'directional_light_color',      'default' => '0xffffff',    'title' => 'Directional light color (hex)',    'type' => 'text',       'section' => 'dlight'),
            array('name' => 'directional_light_position', 'default' => '0, -2*dimensions.y, 1.1*dimensions.z',    'title' => 'Directional light position (x, y, z)',   'type' => 'text',       'section' => 'dlight'),

            array('name' => 'rotation_x', 			'default' => '0', 'title' => 'Rotate object (X-Axis) in Deg.', 	'type' => 'text',		'section' => 'default_rot'),
            array('name' => 'rotation_y', 			'default' => '0', 'title' => 'Rotate object (Y-Axis) in Deg.', 	'type' => 'text',		'section' => 'default_rot'),
            array('name' => 'rotation_z', 			'default' => '0', 'title' => 'Rotate object (Z-Axis) in Deg.', 	'type' => 'text',		'section' => 'default_rot'),

            array('name' => 'webgl_test_success', 	'default' => 'WebGL is supported by your system.', 'title' => 'Success message', 		    'type' => 'textarea',	'section' => 'webgl_test'),
            array('name' => 'webgl_test_fail', 		'default' => 'WebGL is not supported by your system.', 'title' => 'Fail message',       'type' => 'textarea',	'section' => 'webgl_test'),
        );

        // Holds the helptext for the sections
        private $helptext = array(
            'general'       => 'General settings.',
            'render'        => 'How the model will be rendered.',
            'default_rot'   => 'Rotate the model',
            'webgl_test'    => 'If you insert the shortcode [webgl_test] a WebGL test is run and will print the success- or fail-message.',
            'fog'           => 'Set the parameters for the fog.',
            'alight'        => 'Set the parameters for the ambient light.',
            'plight'        => 'Set the parameters for the point light.',
            'dlight'        => 'Set the parameters for the directional light.',
        );

        // Class functions

        public function __construct() {
            // Initialize the settings
            foreach($this->tabs as $tab_key => $tab_caption) {
                add_action('admin_init', array(&$this, 'init_settings_tab_'.$tab_key));
            }
            add_action('admin_menu', array(&$this, 'add_menu'));
        }
        public function getSettingsArray() {
            return $this->settings;
        }

        public function getSettingPrefix() {
            return self::SETTINGS_PREFIX;
        }

        public function getMenuSlug() {
            return self::MENU_SLUG;
        }

        // Return the helptext for a section
        public function getHelptext( $arg ) {
            echo $this->helptext[$arg['id']];
        }

        // Return the tab for the actual field
        private function getTab($field) {
            $tab = 'default';
            foreach( $this->sections as $section ) {
                if( $section['name'] == $field['section']) $tab = $section['tab'];
            }
            return $tab;
        }

        // Functions for the callbacks
        public function init_settings_tab_default()       { $this->init_settings('default');    }
        public function init_settings_tab_render()        { $this->init_settings('render');     }
        public function init_settings_tab_lights()        { $this->init_settings('lights'); }
        public function init_settings_tab_webgl_test()    { $this->init_settings('webgl_test'); }
        public function init_settings_tab_help()          {        }

        // Function called for each tab by the callback function
        private function init_settings($tab) {
            foreach ($this->settings as $field) {
                if ($tab == $this->getTab($field)) $this->setup_field($field);
            }
            foreach ($this->sections as $section) {
                if ($tab == $section['tab']) $this->setup_section($section);
            }
        }

        // Used inside render_settings, just to keep code clean.
        private function setup_field($field) {
            register_setting($this->getTab($field), self::SETTINGS_PREFIX.$field['name']);
            $title = $field['title'].' [<i>'.self::SETTINGS_PREFIX.$field['name'].'</i>]';
            add_settings_field(self::SETTINGS_PREFIX.$field['name'], $title, array(&$this, $field['type']), $this->getTab($field), $field['section'], array('field' => self::SETTINGS_PREFIX.$field['name']));
        }
        private function setup_section($section) {
            add_settings_section( $section['name'], $section['title'], array(&$this, 'getHelptext'), $section['tab']);
        }

        // These function get the option value from DB and render the field
        public function text($args) { 											// This function provides text inputs for settings fields
            $field = $args['field']; 											// Get the field name from the $args array
            $value = get_option($field); 										// Get the value of this setting
            echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value); 		// The input field
        }
        public function textarea($args) { 										// This function provides textarea inputs for settings fields
            $field = $args['field']; 											// Get the field name from the $args array
            $value = get_option($field); 										// Get the value of this setting
            echo sprintf('<textarea name="%s" id="%s" cols="50" rows="5">%s</textarea>', $field, $field, $value);  	// The textarea tag
        }
        public function checkbox($args) {										// This function provides checkbox inputs for settings fields
            $field = $args['field']; 											// Get the field name from the $args array
            $value = get_option($field); 										// Get the value of this setting

            if (!empty($value)) $checked = 'checked';
            else $value = 'true';

            echo sprintf('<input type="checkbox" name="%s" id="%s" value="%s" %s/>', $field, $field, $value, $checked);	// The checkbox tag

        }

        // Register the menu in WordPress
        public function add_menu() {
            add_options_page(self::PAGE_TITLE, self::MENU_TITLE, self::CAPABILITY, self::MENU_SLUG, array(&$this, 'plugin_settings_page'));
        }

        // Gives out the settings page with echo
        public function display_options() {
            $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'default';
            echo '<div class="wrap">';
                $this->plugin_options_tabs();
                if($tab == 'help') {
                    include_once(sprintf( "%s/help.php", dirname(__FILE__) ));
                }
                else {
                    echo '<form method="post" action="options.php">';
                    wp_nonce_field( 'update-options' );
                    settings_fields( $tab );
                    do_settings_sections( $tab );
                    submit_button();
                    echo '</form>';
                }
            echo '</div>';
        }

        // Gives out the tab-navbar with echo
        public function plugin_options_tabs() {
            $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'default';

            echo '<h2>STL Viewer plugin settings</h2>';
            echo '<h2 class="nav-tab-wrapper">';
            foreach ( $this->tabs as $tab_key => $tab_caption ) {
                $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
                echo '<a class="nav-tab ' . $active . '" href="?page=' . self::MENU_SLUG . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
            }
            echo '</h2>';
        }

        // Checks if the user is allowed to acces the page
        // and display the page with display_options()
        public function plugin_settings_page() { 									// Menu Callback
            if(!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            $this->display_options();
        }

    }
}