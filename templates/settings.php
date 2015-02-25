<div class="wrap">
<?php screen_icon(); ?>
    <h2>STL Viewer</h2>
    <form method="post" action="options.php"> 
        <?php @settings_fields('settings-group'); ?>
        <?php @do_settings_fields('settings-group'); ?>

        <?php do_settings_sections('stlviewer'); ?>

        <?php @submit_button(); ?>
    </form>
</div>
