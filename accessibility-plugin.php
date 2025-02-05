/**
 * Renderowanie markup paska narzędziowego
 */
public function render_toolbar_markup() {
    if ( ! get_option( 'accessibility_show_toolbar', 1 ) ) {
        return;
    }

    // Zapobiega wielokrotnemu wyświetlaniu paska
    static $toolbar_displayed = false;
    if ( $toolbar_displayed ) {
        return;
    }
    $toolbar_displayed = true;

    // Ustal domyślny rozmiar czcionki (możesz dostosować wg potrzeb)
    $default_font_size = 16; // Możesz dostosować tę wartość

    ?>
    <div id="accessibility-toolbar" aria-label="<?php esc_attr_e( 'Accessibility Toolbar', 'accessibility-enhancements' ); ?>">
        <button id="accessibility-btn-increase" title="<?php esc_attr_e( 'Increase Text', 'accessibility-enhancements' ); ?>">A+</button>
        <button id="accessibility-btn-decrease" title="<?php esc_attr_e( 'Decrease Text', 'accessibility-enhancements' ); ?>">A-</button>
        <span id="current-font-size"><?php echo esc_html( $default_font_size ); ?>px</span> <!-- Dodany element -->
        <button id="accessibility-btn-contrast" title="<?php esc_attr_e( 'High Contrast Mode', 'accessibility-enhancements' ); ?>">Contrast</button>
        <button id="accessibility-btn-reset" title="<?php esc_attr_e( 'Reset Settings', 'accessibility-enhancements' ); ?>">Reset</button>
    </div>
    <?php
}