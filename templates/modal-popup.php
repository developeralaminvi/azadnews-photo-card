<?php
/**
 * Frontend Modal Popup Template for Azad News Photo Card
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = Azad_Photo_Card_Settings::get_options();
?>
<div id="azad_photo_card_modal" class="azad-modal-overlay" style="display: none;" aria-hidden="true" role="dialog" aria-labelledby="azad_modal_title">
    <div class="azad-modal-backdrop"></div>
    <div class="azad-modal-dialog">
        <!-- Modal Header -->
        <div class="azad-modal-header">
            <h2 id="azad_modal_title" class="azad-modal-title">
                <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" style="vertical-align: -3px; margin-right: 8px;">
                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                </svg>
                Photo Card Preview
            </h2>
            <button type="button" class="azad-modal-close" id="azad_close_modal_btn" aria-label="Close modal">
                <svg viewBox="0 0 24 24" width="20" height="20" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Modal Body: 2 Columns (Preview on Left, Customize on Right) -->
        <div class="azad-modal-body">
            <!-- Left Side: Live Photo Card Preview Container -->
            <div class="azad-preview-area">
                <div class="azad-card-wrapper" id="azad_card_export_target">
                    <div id="azad_photocard_element" class="azad-photocard-container">
                        <!-- Photo card DOM injected and updated by JS -->
                    </div>
                </div>
            </div>

            <!-- Right Side: Customize Control Panel (Matching Image 2) -->
            <div class="azad-controls-area">
                <div class="azad-customize-panel">
                    <h3 class="azad-panel-title">Customize</h3>

                    <!-- Title Font Size Slider -->
                    <div class="azad-control-group">
                        <div class="azad-control-header">
                            <label class="azad-label" for="azad_slider_title_size">Title Font Size</label>
                            <span class="azad-val-badge" id="val_title_size"><?php echo esc_html($options['default_title_size']); ?>px</span>
                        </div>
                        <div class="azad-slider-track-wrap">
                            <input type="range" id="azad_slider_title_size" class="azad-range-input" min="18" max="42" step="1" value="<?php echo esc_attr($options['default_title_size']); ?>" />
                        </div>
                    </div>

                    <!-- Line Height Slider -->
                    <div class="azad-control-group">
                        <div class="azad-control-header">
                            <label class="azad-label" for="azad_slider_line_height">Line Height</label>
                            <span class="azad-val-badge" id="val_line_height"><?php echo esc_html($options['default_line_height']); ?></span>
                        </div>
                        <div class="azad-slider-track-wrap">
                            <input type="range" id="azad_slider_line_height" class="azad-range-input" min="1.05" max="1.90" step="0.05" value="<?php echo esc_attr($options['default_line_height']); ?>" />
                        </div>
                    </div>

                    <!-- Bottom Text Font Size Slider -->
                    <div class="azad-control-group">
                        <div class="azad-control-header">
                            <label class="azad-label" for="azad_slider_bottom_size">Bottom Text Font Size</label>
                            <span class="azad-val-badge" id="val_bottom_size"><?php echo esc_html($options['default_bottom_size']); ?>px</span>
                        </div>
                        <div class="azad-slider-track-wrap">
                            <input type="range" id="azad_slider_bottom_size" class="azad-range-input" min="14" max="30" step="1" value="<?php echo esc_attr($options['default_bottom_size']); ?>" />
                        </div>
                    </div>

                    <!-- Quick Edit Accordion (Optional) -->
                    <div class="azad-quick-edit-accordion">
                        <button type="button" class="azad-accordion-toggle" id="azad_toggle_quick_edit">
                            <span>টেক্সট ও রিপোর্টার সম্পাদনা</span>
                            <span class="azad-chevron">▼</span>
                        </button>
                        <div class="azad-accordion-body" id="azad_quick_edit_fields" style="display: none;">
                            <div class="azad-form-field">
                                <label>পোস্ট শিরোনাম (Title):</label>
                                <textarea id="azad_input_custom_title" rows="2" class="azad-input-text"></textarea>
                            </div>
                            <div class="azad-form-field">
                                <label>প্রতিবেদক (Reporter):</label>
                                <input type="text" id="azad_input_custom_reporter" class="azad-input-text" />
                            </div>
                            <div class="azad-form-field">
                                <label>তারিখ (Date):</label>
                                <input type="text" id="azad_input_custom_date" class="azad-input-text" />
                            </div>
                        </div>
                    </div>

                    <!-- Download Button (Vibrant Green matching Screenshot) -->
                    <div class="azad-action-wrap">
                        <button type="button" class="azad-download-card-btn" id="azad_download_card_btn">
                            <span class="azad-btn-spinner" style="display: none;"></span>
                            <span class="azad-btn-text">Download Photo Card</span>
                        </button>
                    </div>

                    <!-- Helper Notice Text -->
                    <div class="azad-helper-notice">
                        <p>Font size ও line height পরিবর্তন করে <strong>Download Photo Card</strong> চাপুন। Featured image না থাকলে আগে post এ featured image দিন।</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
