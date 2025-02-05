<?php
/**
 * Plugin Name: Accessibility Enhancements
 * Description: An accessibility plugin that allows users to adjust text size, toggle high contrast mode, and reset settings. Compatible with translation plugins like Polylang and WPML. Works independently of the theme.
 * Version: 1.0
 * Author: Jakub Pawluk
 * Author URI: https://dogemeister.pl
 * License: GPL2
 * Text Domain: accessibility-enhancements
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Prevent direct access

// Define plugin directory and load textdomain for translations.
define( 'ACCESSIBILITY_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Load plugin textdomain for translations.
 */
function accessibility_load_textdomain() {
    load_plugin_textdomain( 'accessibility-enhancements', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'accessibility_load_textdomain' );

/**
 * Enqueue plugin scripts and styles.
 */
function accessibility_enqueue_assets() {
    wp_enqueue_style( 'accessibility-plugin-style', plugins_url( 'assets/css/accessibility.css', __FILE__ ) );
    wp_enqueue_script( 'accessibility-plugin-script', plugins_url( 'assets/js/accessibility.js', __FILE__ ), array( 'jquery' ), '1.0', true );

    // Pass plugin configuration settings to the script.
    $plugin_config = array(
        'textStep'    => (int) get_option( 'accessibility_text_step', 2 ),
        'minTextSize' => (int) get_option( 'accessibility_min_text_size', 10 ),
        'maxTextSize' => (int) get_option( 'accessibility_max_text_size', 40 ) // default max = 40px
    );
    wp_localize_script( 'accessibility-plugin-script', 'accessibility_config', $plugin_config );
}
add_action( 'wp_enqueue_scripts', 'accessibility_enqueue_assets' );

/**
 * Display the accessibility toolbar.
 */
function accessibility_toolbar_markup() {
    static $toolbar_displayed = false;
    if ( $toolbar_displayed ) {
        return;
    }
    $toolbar_displayed = true;
    
    if ( ! get_option( 'accessibility_show_toolbar', 1 ) ) {
        return;
    }
    ?>
    <div id="accessibility-toolbar">
        <button id="accessibility-btn-increase" title="<?php esc_attr_e( 'Increase Text', 'accessibility-enhancements' ); ?>">A+</button>
        <button id="accessibility-btn-decrease" title="<?php esc_attr_e( 'Decrease Text', 'accessibility-enhancements' ); ?>">A-</button>
        <button id="accessibility-btn-contrast" title="<?php esc_attr_e( 'High Contrast Mode', 'accessibility-enhancements' ); ?>">Contrast</button>
        <button id="accessibility-btn-reset" title="<?php esc_attr_e( 'Reset Settings', 'accessibility-enhancements' ); ?>">Reset</button>
    </div>
    <?php
}

// If the theme supports wp_body_open, insert the toolbar immediately after the <body> tag is opened,
// otherwise, attach it to the wp_footer action.
add_action( 'wp_body_open', 'accessibility_toolbar_markup' );
add_action( 'wp_footer', 'accessibility_toolbar_markup' );

// Add plugin settings panel to the admin dashboard.
if ( is_admin() ) {
    add_action( 'admin_menu', 'accessibility_register_settings_page' );
    add_action( 'admin_init', 'accessibility_register_settings' );
}

/**
 * Register the settings page in the admin dashboard.
 */
function accessibility_register_settings_page() {
    add_options_page(
        __( 'Accessibility Settings', 'accessibility-enhancements' ),
        __( 'Accessibility', 'accessibility-enhancements' ),
        'manage_options',
        'accessibility-enhancements-settings',
        'accessibility_settings_page'
    );
}

/**
 * Register plugin settings in WordPress.
 */
function accessibility_register_settings() {
    register_setting( 'accessibility-enhancements-settings-group', 'accessibility_show_toolbar' );
    register_setting( 'accessibility-enhancements-settings-group', 'accessibility_text_step' );
    register_setting( 'accessibility-enhancements-settings-group', 'accessibility_min_text_size' );
    register_setting( 'accessibility-enhancements-settings-group', 'accessibility_max_text_size' );
}

/**
 * Display plugin settings page.
 */
function accessibility_settings_page() {
    ?>
    <div class="wrap">
        <h1><?php _e( 'Accessibility Settings', 'accessibility-enhancements' ); ?></h1>
        <form method="post" action="options.php">
            <?php settings_fields( 'accessibility-enhancements-settings-group' ); ?>
            <?php do_settings_sections( 'accessibility-enhancements-settings-group' ); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e( 'Show Accessibility Toolbar', 'accessibility-enhancements' ); ?></th>
                    <td>
                        <input type="checkbox" name="accessibility_show_toolbar" value="1" <?php checked( get_option( 'accessibility_show_toolbar', 1 ), 1 ); ?> />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Text Size Increment', 'accessibility-enhancements' ); ?></th>
                    <td>
                        <input type="number" name="accessibility_text_step" value="<?php echo esc_attr( get_option('accessibility_text_step', 2) ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Minimum Font Size', 'accessibility-enhancements' ); ?></th>
                    <td>
                        <input type="number" name="accessibility_min_text_size" value="<?php echo esc_attr( get_option('accessibility_min_text_size', 10) ); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row"><?php _e( 'Maximum Font Size', 'accessibility-enhancements' ); ?></th>
                    <td>
                        <input type="number" name="accessibility_max_text_size" value="<?php echo esc_attr( get_option('accessibility_max_text_size', 40) ); ?>" />
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
