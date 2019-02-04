<?php
/**
 * Welcome screen getting started template
 */
?>
<?php $theme_data = wp_get_theme('base-wp'); ?>
<h1 class="theme-name">
    <?php echo $theme_data->Name .'<sup class="version">' . esc_attr(  $theme_data->Version ) . '</sup>'; ?>
</h1>
<p><?php esc_html_e( 'Here you can read the documentation and know how to get the most out of your new theme.', 'base-wp' ); ?></p>
<div id="getting_started" class="panel">
    <div class="col2 evidence">
        <h3><?php printf(esc_html__('%s Premium', 'base-wp'), $theme_data->Name); ?></h3>
           <p>
                <?php esc_html_e( 'Base WP Premium expands the already powerful free version of this theme and gives access to our priority support service.', 'base-wp' ); ?>
            <ul>
                <li><?php esc_html_e( 'More advanced options', 'base-wp' ); ?></li>
                <li><?php esc_html_e( 'Different menu layout', 'base-wp' ); ?></li>
                <li><?php esc_html_e( 'Blog customizer', 'base-wp' ); ?></li>
                <li><?php esc_html_e( 'Shop customizer', 'base-wp' ); ?></li>
                <li><?php esc_html_e( 'Custom fonts', 'base-wp' ); ?></li>
                <li><?php esc_html_e( 'New post and page settings', 'base-wp' ); ?></li>
                <li><?php esc_html_e( 'New advanced widgets', 'base-wp' ); ?></li>
                <li><?php esc_html_e( 'Premium shortcodes', 'base-wp' ); ?></li>
                <li><?php esc_html_e( 'Premium support', 'base-wp' ); ?></li>
                <li><?php esc_html_e( 'Money back guarantee', 'base-wp' ); ?></li>
            </ul>
            <a href="<?php echo esc_url( 'https://iograficathemes.com/theme/'. $theme_data->get( 'TextDomain' )); ?>" target="_blank" class="button-upgrade">
                <?php esc_html_e('view all premium features', 'base-wp'); ?>
            </a>
        </p>
    </div>
     <div class="col2">
        <h3><?php esc_html_e( 'Enjoying the theme?', 'base-wp' ); ?></h3>
        <p class="about"><?php esc_html_e( 'If you like this theme why not leave us a review on WordPress.org?  We\'d really appreciate it!', 'base-wp' ); ?></p>
        <p>
            <a href="<?php echo esc_url( 'https://wordpress.org/support/theme/'. $theme_data->get( 'TextDomain' ) .'/reviews/#new-post' ); ?>" target="_blank" class="button button-secondary"><?php esc_html_e('Add Your Review', 'base-wp'); ?></a>
        </p>
        <h3><?php esc_html_e( 'Theme Documentation', 'base-wp' ); ?></h3>
        <p class="about"><?php printf(esc_html__('Need any help to setup and configure %s? Please have a look at our documentations instructions.', 'base-wp'), $theme_data->Name); ?></p>
        <p>
            <a href="<?php echo esc_url( 'https://www.iograficathemes.com/documentation/'. $theme_data->get( 'TextDomain' )); ?>" target="_blank" class="button button-secondary"><?php esc_html_e('View Documentation', 'base-wp'); ?></a>
        </p>
        <h3><?php esc_html_e( 'Theme Customizer', 'base-wp' ); ?></h3>
        <p class="about"><?php printf(esc_html__('%s supports the Theme Customizer for all theme settings. Click "Customize" to start customize your site.', 'base-wp'), $theme_data->Name); ?></p>
        <p>
            <a href="<?php echo admin_url('customize.php'); ?>" class="button button-secondary"><?php esc_html_e('Start Customize', 'base-wp'); ?></a>
        </p>
    </div>

</div><!-- end ig-started -->
