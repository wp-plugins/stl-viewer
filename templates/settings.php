<div class="wrap">
    <h2>WP Plugin Template</h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('stlviewer_settings-group'); ?>
        <?php @do_settings_fields('stlviewer_settings-group'); ?>

        <?php do_settings_sections('stlviewer'); ?>

        <?php @submit_button(); ?>
    </form>
</div>
