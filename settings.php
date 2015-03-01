<?php
if(!class_exists('STLViewer_Settings')) {
    class STLViewer_Settings { 							// Construct the plugin object

        private $tabs = array(
            'default'       => 'General Settings',
            'render'        => 'Rendering options',
            'webgl_test'    => 'WebGL Test options',
            'misc'          => 'Misc'
        );          // Holding all tabs. Don't forget to add a callback for each tab!
        private $sections = array(
            array('name' => 'general', 		'title' =>'General settings',    'tab' => 'default'),
            array('name' => 'render', 		'title' =>'WebGL renderer settings',  'tab' => 'render'),
            array('name' => 'webgl_test', 	'title' =>'WebGL tester settings',  'tab' => 'webgl_test')
        );      // Holding all sections in all tabs
        private $settings = array(
            array('name' => 'height', 				'title' => 'Height (height) ', 		    'type' => 'text',		'section' => 'general'),
            array('name' => 'width', 				'title' => 'Width (width)', 		    'type' => 'text',		'section' => 'general'),
            array('name' => 'stl_div_webgl_error', 	'title' => 'WebGL error message', 	    'type' => 'textarea',	'section' => 'general'),
            array('name' => 'stl_div_informations',  'title' => 'Informations', 			    'type' => 'textarea',	'section' => 'general'),
            array('name' => 'stl_div_loading_text',  'title' => 'Loading text', 			    'type' => 'textarea',	'section' => 'general'),
            array('name' => 'floor', 				'title' => 'Floor texture (floor)',     'type' => 'text',		'section' => 'render'),
            array('name' => 'rotation', 			    'title' => 'Rotate object (rotation)', 	'type' => 'text',		'section' => 'render'),
            array('name' => 'webgl_test_success', 	'title' => 'Success message', 		    'type' => 'textarea',	'section' => 'webgl_test'),
            array('name' => 'webgl_test_fail', 		'title' => 'Fail message', 			    'type' => 'textarea',	'section' => 'webgl_test'),
        );      // Holding all settings fields in all sections
        private $helptext = array(
            'general'       => 'These settings do things for the WP Plugin Template.',
            'webgl_test'    => 'If you insert the shortcode [webgl_test] a WebGL test is run and will print the success- or fail-message.',
            'render'        => 'How the model will be rendered.',
        );

        private $page_title = 'STL Viewer Settings';
        private $menu_title = 'STL Viewer';
        private $capability = 'manage_options'; //Who is allowed to change settings
        private $menu_slug = 'stlviewer'; // Also the page name

        public function __construct() {
            foreach($this->tabs as $tab_key => $tab_caption) { add_action('admin_init', array(&$this, 'render_settings_tab_'.$tab_key)); }
            add_action('admin_menu', array(&$this, 'add_menu'));
        }

        // Return the helptext for a section
        // Todo: is a callback, can't take any options -> fix
        private function helptext($section) {
            //echo $this->helptext[$section];
        }

        // Return the tab for the actual field
        private function getTab($field) {
            $tab = 'default';
            foreach( $this->sections as $section ) { if( $section['name'] == $field['section']) $tab = $section['tab']; }
            return $tab;
        }

        // Functions for the callbacks
        // These function are called by render_settings()
        // Todo: change name from render_ to something else to prevent confusion
        public function render_settings_tab_default()       { $this->render_settings('default');    }
        public function render_settings_tab_render()        { $this->render_settings('render');     }
        public function render_settings_tab_webgl_test()    { $this->render_settings('webgl_test'); }
        public function render_settings_tab_misc()          { $this->render_settings('misc');       }

        private function render_settings($tab) {
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

        public function add_menu() {
            add_options_page($this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array(&$this, 'plugin_settings_page'));
        }

        public function plugin_settings_page() { 									// Menu Callback
            if(!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            include( sprintf( "%s/templates/settings.php", dirname(__FILE__) ) );
        }

        public function display_options() {
            $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'default';

            // Output HTML Content
            ?>
            <div class="wrap">
                <?php $this->plugin_options_tabs(); ?>
                <form method="post" action="options.php">
                    <?php wp_nonce_field( 'update-options' ); ?>
                    <?php settings_fields( $tab ); ?>
                    <?php //do_settings_fields( $tab ); ?>
                    <?php do_settings_sections( $tab ); ?>

                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
            // End of HTML output

        }

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

    }
}

$STLViewer_Settings = new STLViewer_Settings();