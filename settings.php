<?php
if(!class_exists('STLViewer_Settings')) {
    class STLViewer_Settings {

        // Add the tabs for the settings page here
        // For each tab you need to create a callback function
        // public function init_settings_tab_tabname() { $this->init_settings('tabname'); }
        //
        private $tabs = array(
            'default'       => '<span class="dashicons dashicons-admin-settings"></span> General Settings',
            'render'        => '<span class="dashicons dashicons-desktop"></span> Rendering options',
            'webgl_test'    => '<span class="dashicons dashicons-admin-generic"></span> WebGL Test options',
            //'misc'          => 'Misc'
        );

        // Empty array('name' => '', 'title' => '', 'tab' => ''),
        // Don't forget to specify a helptext, even if it's empty.
        private $sections = array(
            array('name' => 'general', 		'title' =>'General settings',           'tab' => 'default'),
            array('name' => 'render', 		'title' =>'WebGL renderer settings',    'tab' => 'render'),
            array('name' => 'default_rot', 	'title' =>'Default Object rotation',    'tab' => 'render'),
            array('name' => 'webgl_test', 	'title' =>'WebGL tester settings',      'tab' => 'webgl_test'),
            array('name' => 'fog', 	        'title' =>'Fog settings',               'tab' => 'render'),
            array('name' => 'alight', 	    'title' =>'Ambient light settings',     'tab' => 'render'),
        );

        // Empty array('name' => '', 'title' => '', 'type' => '', 'section' => ''),
        private $settings = array(
            array('name' => 'height', 				'title' => 'Height (height)', 		    'type' => 'text',		'section' => 'general'),
            array('name' => 'width', 				'title' => 'Width (width)', 		    'type' => 'text',		'section' => 'general'),
            array('name' => 'stl_div_webgl_error', 	'title' => 'WebGL error message', 	    'type' => 'textarea',	'section' => 'general'),
            array('name' => 'stl_div_informations', 'title' => 'Informations', 			    'type' => 'textarea',	'section' => 'general'),
            array('name' => 'stl_div_loading_text', 'title' => 'Loading text', 			    'type' => 'textarea',	'section' => 'general'),

            array('name' => 'floor', 			    'title' => 'Floor texture (floor)',     'type' => 'text',		'section' => 'render'),
            array('name' => 'show_controls', 	    'title' => 'Show controls',             'type' => 'checkbox',	'section' => 'render'),
            array('name' => 'autorotate', 			'title' => 'Enable autorotation',        'type' => 'checkbox',	'section' => 'render'),

            array('name' => 'fog_enable',   'title' => 'Enable fog',        'type' => 'checkbox',   'section' => 'fog'),
            array('name' => 'fog_color',    'title' => 'Fog color (hex)',   'type' => 'text',       'section' => 'fog'),
            array('name' => 'fog_near',     'title' => 'Min. fog distance', 'type' => 'text',       'section' => 'fog'),
            array('name' => 'fog_far',      'title' => 'Max. fog distance', 'type' => 'text',       'section' => 'fog'),

            array('name' => 'ambient_light_color',  'title' => 'Ambient light color (hex)',         'type' => 'text',       'section' => 'alight'),
            array('name' => 'ambient_light_int',    'title' => 'Ambient light intensity (0..1)',    'type' => 'text',       'section' => 'alight'),

            array('name' => 'rotation_x', 			'title' => 'Rotate object (X-Axis) in Deg.', 	'type' => 'text',		'section' => 'default_rot'),
            array('name' => 'rotation_y', 			'title' => 'Rotate object (Y-Axis) in Deg.', 	'type' => 'text',		'section' => 'default_rot'),
            array('name' => 'rotation_z', 			'title' => 'Rotate object (Z-Axis) in Deg.', 	'type' => 'text',		'section' => 'default_rot'),

            array('name' => 'webgl_test_success', 	'title' => 'Success message', 		    'type' => 'textarea',	'section' => 'webgl_test'),
            array('name' => 'webgl_test_fail', 		'title' => 'Fail message', 			    'type' => 'textarea',	'section' => 'webgl_test'),
        );

        // Holds the helptext for the sections
        private $helptext = array(
            'general'       => 'These settings do things for the WP Plugin Template.',
            'render'        => 'How the model will be rendered.',
            'default_rot'   => 'Rotate the model',
            'webgl_test'    => 'If you insert the shortcode [webgl_test] a WebGL test is run and will print the success- or fail-message.',
            'fog'           => 'Set the parameters for the fog.',
            'alight'        => 'Set the parameters for the ambient light.',
        );

        // General settings for the settings page / class
        private $page_title = 'STL Viewer Settings';// Page title
        private $menu_title = 'STL Viewer';         // Title in the menu
        private $capability = 'manage_options';     // Who is allowed to change settings
        private $menu_slug = 'stlviewer';           // Also the page name

        // Class functions

        public function __construct() {
            foreach($this->tabs as $tab_key => $tab_caption) {
                add_action('admin_init', array(&$this, 'init_settings_tab_'.$tab_key));
            }
            add_action('admin_menu', array(&$this, 'add_menu'));
        }

        // Return the helptext for a section
        public function helptext( $arg ) {
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
        public function init_settings_tab_webgl_test()    { $this->init_settings('webgl_test'); }
        public function init_settings_tab_misc()          { $this->init_settings('misc');       }

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
            register_setting($this->getTab($field), $field['name']);
            add_settings_field($field['name'], $field['title'], array(&$this, $field['type']), $this->getTab($field), $field['section'], array('field' => $field['name']));
        }
        private function setup_section($section) {
            add_settings_section( $section['name'], $section['title'], array(&$this, 'helptext'), $section['tab']);
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
            add_options_page($this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(&$this, 'plugin_settings_page'));
        }

        // Gives out the settings page with echo
        public function display_options() {
            $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'default';
            ?>
            <div class="wrap">
                <?php $this->plugin_options_tabs(); ?>
                <form method="post" action="options.php">
                    <?php wp_nonce_field( 'update-options' ); ?>
                    <?php settings_fields( $tab ); ?>
                    <?php do_settings_sections( $tab ); ?>
                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
        }

        // Gives out the tab-navbar with echo
        public function plugin_options_tabs() {
            $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'default';

            echo '<h2>STL Viewer plugin settings</h2>';
            echo '<h2 class="nav-tab-wrapper">';
            foreach ( $this->tabs as $tab_key => $tab_caption ) {
                $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
                echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->menu_slug . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
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

$STLViewer_Settings = new STLViewer_Settings();