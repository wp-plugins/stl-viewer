<?php
if(!class_exists('STLViewer_Settings')) {

    class STLViewer_Settings { 							// Construct the plugin object

        private $tabs = array(
            'stl-default'   => 'General Settings',
            'stl-test'      => 'WebGL Test'
        );

        private $sections = array(
            array('name' => 'general', 		'title' =>'STL viewer settings',    'tab' => 'stl-default'),
            array('name' => 'render', 		'title' =>'WebGL render settings',  'tab' => 'stl-default'),
            array('name' => 'webgl_test', 	'title' =>'WebGL tester settings',  'tab' => 'stl-test')
        );
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
        );
        private $helptext = array(
            'general'       => 'These settings do things for the WP Plugin Template.',
            'webgl_test'    => 'If you insert the shortcode [webgl_test] a WebGL test is run and will print the success- or fail-message.',
            'render'        => 'How the model will be rendered.',
        );

        public function helptext($section) {
            echo $this->helptext[$section];
        }

        private function getTab($field) {
            while( $section = current($this->sections)) {
                if( $section['name'] == $field['section']) return $section['tab'];
                else next($this->sections);
            }
        }

        public function __construct() {
            // register actions
            foreach($this->tabs as $tab) {
                add_action('admin_init', array(&$this, 'render_settings(' . $tab . ')'));
            }
            add_action('admin_menu', array(&$this, 'add_menu'));
        } // END public function __construct

        function setup_field($field) {
            register_setting($this->getTab($field), $field['name']);
            add_settings_field($field['name'], $field['title'], array(&$this, $field['type']), 'stlviewer', $field['section'], array('field' => $field['name']));
        }
        function setup_section($section) {
            add_settings_section( $section['name'], $section['title'], array(&$this, 'helptext('.$section['name'].')'), 'stlviewer');
        }

        public function render_settings($tab) {
            foreach( $this->settings as $field) {
                if($tab == $this->getTab($field)) $this->setup_field($field);
            }
            foreach( $this->sections as $section ) {
                if($tab == $section['tab']) $this->setup_section($section);
            }

        } // END public static function activate

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

        public function add_menu() { 											// Add a page to manage this plugin's settings
            add_options_page('STL Viewer Settings', 'STL Viewer', 'manage_options', 'stlviewer', array(&$this, 'plugin_settings_page'));
        } // END public function add_menu()

        public function plugin_settings_page() { 									// Menu Callback
            if(!current_user_can('manage_options')) {
                wp_die(__('You do not have sufficient permissions to access this page.'));
            }
            include( sprintf( "%s/templates/settings.php", dirname(__FILE__) ) );
        }

        function display_options() {
            $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'default';

            // Output HTML Content
            ?>
            <div class="wrap">
                <?php $this->plugin_options_tabs(); ?>
                <form method="post" action="options.php">
                    <?php @settings_fields($tab); ?>
                    <?php @do_settings_fields($tab); ?>

                    <?php do_settings_sections('stlviewer'); ?>

                    <?php @submit_button(); ?>
                </form>
            </div>
            <?php
            // End of HTML output

        }

    function plugin_options_tabs() {
        $current_tab = isset( $_GET['ta' .
            'b'] ) ? $_GET['tab'] : 'default';

        echo '<h2 class="nav-tab-wrapper">';
        foreach ( $this->tabs as $tab_key => $tab_caption ) {
            $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
            echo '<a class="nav-tab ' . $active . '" href="?page=' . 'stlviewer' . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
        }
        echo '</h2>';
    }

    }
}