<?php
/**
 * Admin Settings Class for Azad News Photo Card
 */

if (!defined('ABSPATH')) {
    exit;
}

class Azad_Photo_Card_Settings {

    /**
     * Option key
     */
    const OPTION_KEY = 'azad_photo_card_options';

    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }

    /**
     * Register Admin Menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __('Azad Photo Card Settings', 'azadnews-photo-card'),
            __('Azad Photo Card', 'azadnews-photo-card'),
            'manage_options',
            'azad-photo-card',
            array($this, 'render_settings_page'),
            'dashicons-format-image',
            30
        );
    }

    /**
     * Register Settings
     */
    public function register_settings() {
        register_setting('azad_photo_card_group', self::OPTION_KEY, array($this, 'sanitize_settings'));
    }

    /**
     * Enqueue Admin Assets
     */
    public function enqueue_admin_assets($hook) {
        if ($hook !== 'toplevel_page_azad-photo-card') {
            return;
        }

        wp_enqueue_media();
        wp_enqueue_style(
            'azad-google-fonts',
            'https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;600;700;800&family=Hind+Siliguri:wght@400;500;600;700;800&family=Noto+Serif+Bengali:wght@600;700;900&family=Tiro+Bangla&display=swap',
            array(),
            null
        );
        wp_enqueue_style(
            'azad-templates-css',
            AZAD_PHOTO_CARD_URL . 'assets/css/templates.css',
            array(),
            AZAD_PHOTO_CARD_VERSION
        );
        wp_enqueue_style(
            'azad-admin-css',
            AZAD_PHOTO_CARD_URL . 'assets/css/admin-style.css',
            array(),
            AZAD_PHOTO_CARD_VERSION
        );

        wp_enqueue_script(
            'azad-html2canvas',
            AZAD_PHOTO_CARD_URL . 'assets/js/html2canvas.min.js',
            array(),
            '1.4.1',
            true
        );
        wp_enqueue_script(
            'azad-admin-js',
            AZAD_PHOTO_CARD_URL . 'assets/js/admin-script.js',
            array('jquery', 'azad-html2canvas'),
            AZAD_PHOTO_CARD_VERSION,
            true
        );

        $options = self::get_options();
        wp_localize_script('azad-admin-js', 'azadPhotoCardAdmin', array(
            'options'     => $options,
            'pluginUrl'   => AZAD_PHOTO_CARD_URL,
            'bengaliDate' => Azad_Bengali_Date::get_bengali_date(current_time('timestamp')),
            'i18n'        => array(
                'chooseImage'   => __('লোগো আইকন নির্বাচন করুন', 'azadnews-photo-card'),
                'useThisImage'  => __('এই ছবি ব্যবহার করুন', 'azadnews-photo-card'),
                'downloading'   => __('ডাউনলোড হচ্ছে...', 'azadnews-photo-card'),
                'download'      => __('Download Photo Card', 'azadnews-photo-card'),
            )
        ));
    }

    /**
     * Get saved options with defaults
     */
    public static function get_options() {
        $defaults = array(
            'logo_icon_url'         => AZAD_PHOTO_CARD_URL . 'assets/images/logo-icon.svg',
            'footer_text'           => 'আজাদ নিউজ ২৪ | www.azadnews-24.com',
            'default_title_size'    => 27,
            'default_line_height'   => 1.35,
            'default_bottom_size'   => 20,
            'default_font_family'   => "'Hind Siliguri', sans-serif",
            'default_bottom_badge'  => 'বিস্তারিত কমেন্টে',
            'default_reporter_mode' => 'custom',
            'default_reporter_text' => 'কক্সবাজার প্রতিনিধি:',
            'button_position'       => 'below_featured_image',
            'button_text'           => 'Photo Card',
            'export_scale'          => 2,
            'user_access'           => 'all',
        );

        $saved = get_option(self::OPTION_KEY, array());
        return wp_parse_args($saved, $defaults);
    }

    /**
     * Sanitize settings input
     */
    public function sanitize_settings($input) {
        $clean = array();
        $clean['logo_icon_url']         = esc_url_raw($input['logo_icon_url'] ?? '');
        $clean['footer_text']           = sanitize_text_field($input['footer_text'] ?? 'আজাদ নিউজ ২৪ | www.azadnews-24.com');
        $clean['default_title_size']    = absint($input['default_title_size'] ?? 27);
        $clean['default_line_height']   = floatval($input['default_line_height'] ?? 1.35);
        $clean['default_bottom_size']   = absint($input['default_bottom_size'] ?? 20);
        $clean['default_font_family']   = sanitize_text_field($input['default_font_family'] ?? "'Hind Siliguri', sans-serif");
        $clean['default_bottom_badge']  = sanitize_text_field($input['default_bottom_badge'] ?? 'বিস্তারিত কমেন্টে');
        $clean['default_reporter_mode'] = sanitize_text_field($input['default_reporter_mode'] ?? 'custom');
        $clean['default_reporter_text'] = sanitize_text_field($input['default_reporter_text'] ?? 'কক্সবাজার প্রতিনিধি:');
        $clean['button_position']       = sanitize_text_field($input['button_position'] ?? 'below_featured_image');
        $clean['button_text']           = sanitize_text_field($input['button_text'] ?? 'Photo Card');
        $clean['export_scale']          = in_array($input['export_scale'] ?? 2, array(1, 2, 3)) ? intval($input['export_scale']) : 2;
        $clean['user_access']           = sanitize_text_field($input['user_access'] ?? 'all');

        return $clean;
    }

    /**
     * Render Settings Page
     */
    public function render_settings_page() {
        $opts = self::get_options();
        ?>
        <div class="wrap azad-admin-wrap">
            <div class="azad-admin-header">
                <div class="azad-header-left">
                    <div class="azad-header-icon">
                        <span class="dashicons dashicons-format-image"></span>
                    </div>
                    <div>
                        <h1><?php _e('Azad News Photo Card সেটিংস', 'azadnews-photo-card'); ?></h1>
                        <p class="azad-subtitle"><?php _e('সোশ্যাল মিডিয়া ফটো কার্ড অটোমেশন ও ব্র্যান্ডিং সেটিংস', 'azadnews-photo-card'); ?> &bull; <a href="https://azadnews-24.com" target="_blank">azadnews-24.com</a></p>
                    </div>
                </div>
                <div class="azad-header-badge">
                    <span>v1.0.0</span>
                </div>
            </div>

            <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated']) : ?>
                <div class="notice notice-success is-dismissible">
                    <p><strong><?php _e('সেটিংস সফলভাবে সংরক্ষিত হয়েছে!', 'azadnews-photo-card'); ?></strong></p>
                </div>
            <?php endif; ?>

            <div class="azad-admin-layout">
                <!-- Left: Settings Form -->
                <div class="azad-settings-column">
                    <form method="post" action="options.php" id="azad-settings-form">
                        <?php settings_fields('azad_photo_card_group'); ?>

                        <!-- Navigation Tabs -->
                        <nav class="nav-tab-wrapper azad-nav-tabs">
                            <a href="#tab-branding" class="nav-tab nav-tab-active" data-tab="tab-branding">
                                <span class="dashicons dashicons-art"></span> <?php _e('ব্র্যান্ডিং ও লোগো', 'azadnews-photo-card'); ?>
                            </a>
                            <a href="#tab-typography" class="nav-tab" data-tab="tab-typography">
                                <span class="dashicons dashicons-editor-textcolor"></span> <?php _e('টাইপোগ্রাফি ও ডিফল্ট', 'azadnews-photo-card'); ?>
                            </a>
                            <a href="#tab-display" class="nav-tab" data-tab="tab-display">
                                <span class="dashicons dashicons-layout"></span> <?php _e('ডিসপ্লে ও পারমিশন', 'azadnews-photo-card'); ?>
                            </a>
                        </nav>

                        <!-- TAB 1: Branding & Logo -->
                        <div id="tab-branding" class="azad-tab-content azad-tab-active">
                            <div class="azad-card">
                                <h3><?php _e('১. ব্র্যান্ডিং ও লোগো সেটিংস', 'azadnews-photo-card'); ?></h3>
                                <p class="description"><?php _e('ফটো কার্ডের নিচে রিপোর্টারের পাশে প্রদর্শনের জন্য লোগো আইকন আপলোড বা পরিবর্তন করুন।', 'azadnews-photo-card'); ?></p>

                                <div class="azad-form-group">
                                    <label><?php _e('বৃত্তাকার লোগো আইকন (Logo Icon):', 'azadnews-photo-card'); ?></label>
                                    <div class="azad-media-input-group">
                                        <input type="text" name="<?php echo self::OPTION_KEY; ?>[logo_icon_url]" id="azad_logo_icon_url" value="<?php echo esc_attr($opts['logo_icon_url']); ?>" class="regular-text" />
                                        <button type="button" class="button azad-upload-btn" data-target="#azad_logo_icon_url" data-preview="#azad_logo_icon_preview">
                                            <span class="dashicons dashicons-upload"></span> <?php _e('আইকন পরিবর্তন / আপলোড', 'azadnews-photo-card'); ?>
                                        </button>
                                    </div>
                                    <div class="azad-preview-box small">
                                        <img src="<?php echo esc_url($opts['logo_icon_url']); ?>" id="azad_logo_icon_preview" alt="Icon Preview" style="max-height: 50px; border-radius: 50%;" />
                                    </div>
                                </div>

                                <div class="azad-form-group">
                                    <label for="azad_footer_text"><?php _e('ফুটার টেক্সট (Footer Text):', 'azadnews-photo-card'); ?></label>
                                    <input type="text" name="<?php echo self::OPTION_KEY; ?>[footer_text]" id="azad_footer_text" value="<?php echo esc_attr($opts['footer_text']); ?>" class="regular-text" />
                                    <p class="description"><?php _e('যেমন: "আজাদ নিউজ ২৪ |" অথবা "আজাদ নিউজ ২৪ | www.azadnews-24.com"', 'azadnews-photo-card'); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: Typography & Defaults -->
                        <div id="tab-typography" class="azad-tab-content">
                            <div class="azad-card">
                                <h3><?php _e('২. টাইপোগ্রাফি ও ডিফল্ট সেটিংস', 'azadnews-photo-card'); ?></h3>
                                <p class="description"><?php _e('ফটো কার্ডের ডিফল্ট টাইটেল ফন্ট সাইজ, লাইন হাইট ও টেক্সট কনফিগার করুন।', 'azadnews-photo-card'); ?></p>

                                <div class="azad-form-group">
                                    <label for="azad_default_font_family"><?php _e('বাংলা ফন্ট ফ্যামিলি:', 'azadnews-photo-card'); ?></label>
                                    <select name="<?php echo self::OPTION_KEY; ?>[default_font_family]" id="azad_default_font_family" class="regular-text">
                                        <option value="'Hind Siliguri', sans-serif" <?php selected($opts['default_font_family'], "'Hind Siliguri', sans-serif"); ?>>Hind Siliguri (হিন্দ শিলিগুড়ি - রিকমেন্ডেড)</option>
                                        <option value="'Noto Serif Bengali', serif" <?php selected($opts['default_font_family'], "'Noto Serif Bengali', serif"); ?>>Noto Serif Bengali (নোটো সেরিফ বাংলা)</option>
                                        <option value="'Anek Bangla', sans-serif" <?php selected($opts['default_font_family'], "'Anek Bangla', sans-serif"); ?>>Anek Bangla (অনেক বাংলা)</option>
                                        <option value="'Tiro Bangla', serif" <?php selected($opts['default_font_family'], "'Tiro Bangla', serif"); ?>>Tiro Bangla (তিরো বাংলা)</option>
                                        <option value="'SolaimanLipi', Arial, sans-serif" <?php selected($opts['default_font_family'], "'SolaimanLipi', Arial, sans-serif"); ?>>SolaimanLipi (সোলাইমান লিপি)</option>
                                    </select>
                                </div>

                                <div class="azad-form-group">
                                    <label><?php _e('ডিফল্ট টাইটেল ফন্ট সাইজ (Title Font Size):', 'azadnews-photo-card'); ?></label>
                                    <div class="azad-slider-field">
                                        <input type="range" min="18" max="42" value="<?php echo esc_attr($opts['default_title_size']); ?>" class="azad-range-slider" id="range_title_size" />
                                        <input type="number" name="<?php echo self::OPTION_KEY; ?>[default_title_size]" id="azad_default_title_size" value="<?php echo esc_attr($opts['default_title_size']); ?>" min="18" max="42" class="small-text" /> <span>px</span>
                                    </div>
                                </div>

                                <div class="azad-form-group">
                                    <label><?php _e('ডিফল্ট লাইন হাইট (Line Height):', 'azadnews-photo-card'); ?></label>
                                    <div class="azad-slider-field">
                                        <input type="range" min="1.05" max="1.90" step="0.05" value="<?php echo esc_attr($opts['default_line_height']); ?>" class="azad-range-slider" id="range_line_height" />
                                        <input type="number" name="<?php echo self::OPTION_KEY; ?>[default_line_height]" id="azad_default_line_height" value="<?php echo esc_attr($opts['default_line_height']); ?>" min="1.05" max="1.90" step="0.05" class="small-text" />
                                    </div>
                                </div>

                                <div class="azad-form-group">
                                    <label><?php _e('ডিফল্ট বটম টেক্সট ফন্ট সাইজ (Bottom Text Size):', 'azadnews-photo-card'); ?></label>
                                    <div class="azad-slider-field">
                                        <input type="range" min="14" max="30" value="<?php echo esc_attr($opts['default_bottom_size']); ?>" class="azad-range-slider" id="range_bottom_size" />
                                        <input type="number" name="<?php echo self::OPTION_KEY; ?>[default_bottom_size]" id="azad_default_bottom_size" value="<?php echo esc_attr($opts['default_bottom_size']); ?>" min="14" max="30" class="small-text" /> <span>px</span>
                                    </div>
                                </div>

                                <div class="azad-form-group">
                                    <label for="azad_default_bottom_badge"><?php _e('ডিফল্ট কমেন্ট ব্যাজ টেক্সট:', 'azadnews-photo-card'); ?></label>
                                    <input type="text" name="<?php echo self::OPTION_KEY; ?>[default_bottom_badge]" id="azad_default_bottom_badge" value="<?php echo esc_attr($opts['default_bottom_badge']); ?>" class="regular-text" />
                                </div>

                                <div class="azad-form-group">
                                    <label for="azad_default_reporter_text"><?php _e('ডিফল্ট রিপোর্টার টেক্সট:', 'azadnews-photo-card'); ?></label>
                                    <input type="text" name="<?php echo self::OPTION_KEY; ?>[default_reporter_text]" id="azad_default_reporter_text" value="<?php echo esc_attr($opts['default_reporter_text']); ?>" class="regular-text" />
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: Display & Permissions -->
                        <div id="tab-display" class="azad-tab-content">
                            <div class="azad-card">
                                <h3><?php _e('৩. ফ্রন্টএন্ড বাটন ও ডিসপ্লে অপশন', 'azadnews-photo-card'); ?></h3>
                                <div class="azad-form-group">
                                    <label for="azad_button_position"><?php _e('বাটনের অবস্থান (Button Position):', 'azadnews-photo-card'); ?></label>
                                    <select name="<?php echo self::OPTION_KEY; ?>[button_position]" id="azad_button_position" class="regular-text">
                                        <option value="below_featured_image" <?php selected($opts['button_position'], 'below_featured_image'); ?>><?php _e('ফিচার্ড ইমেজের ঠিক নিচে (Below Featured Image - Recommended)', 'azadnews-photo-card'); ?></option>
                                        <option value="after_content" <?php selected($opts['button_position'], 'after_content'); ?>><?php _e('পোস্ট কনটেন্টের শেষে (After Post Content)', 'azadnews-photo-card'); ?></option>
                                        <option value="before_content" <?php selected($opts['button_position'], 'before_content'); ?>><?php _e('পোস্ট কনটেন্টের শুরুতে (Before Post Content)', 'azadnews-photo-card'); ?></option>
                                        <option value="below_title" <?php selected($opts['button_position'], 'below_title'); ?>><?php _e('পোস্ট টাইটেলের নিচে (Below Title)', 'azadnews-photo-card'); ?></option>
                                        <option value="shortcode_only" <?php selected($opts['button_position'], 'shortcode_only'); ?>><?php _e('শুধুমাত্র শর্টকোড মাধ্যমে ([azad_photo_card])', 'azadnews-photo-card'); ?></option>
                                    </select>
                                </div>

                                <div class="azad-form-group">
                                    <label for="azad_button_text"><?php _e('বাটন টেক্সট:', 'azadnews-photo-card'); ?></label>
                                    <input type="text" name="<?php echo self::OPTION_KEY; ?>[button_text]" id="azad_button_text" value="<?php echo esc_attr($opts['button_text']); ?>" class="regular-text" />
                                </div>

                                <div class="azad-form-group">
                                    <label for="azad_export_scale"><?php _e('ইমেজ রেজুলেশন / এক্সপোর্ট কোয়ালিটি:', 'azadnews-photo-card'); ?></label>
                                    <select name="<?php echo self::OPTION_KEY; ?>[export_scale]" id="azad_export_scale" class="regular-text">
                                        <option value="2" <?php selected($opts['export_scale'], 2); ?>><?php _e('Ultra HD 2X (1200x1200 px - ক্রিস্টাল ক্লিয়ার)', 'azadnews-photo-card'); ?></option>
                                        <option value="1" <?php selected($opts['export_scale'], 1); ?>><?php _e('Standard 1X (600x600 px)', 'azadnews-photo-card'); ?></option>
                                        <option value="3" <?php selected($opts['export_scale'], 3); ?>><?php _e('Super 3X (1800x1800 px)', 'azadnews-photo-card'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="azad-submit-bar">
                            <?php submit_button(__('সেটিংস সংরক্ষণ করুন', 'azadnews-photo-card'), 'primary', 'submit', false); ?>
                        </div>
                    </form>
                </div>

                <!-- Right: Live Admin Preview & Test Generator -->
                <div class="azad-preview-column">
                    <div class="azad-card azad-sticky-preview">
                        <div class="azad-preview-header">
                            <h3><span class="dashicons dashicons-visibility"></span> <?php _e('লাইভ ফটো কার্ড প্রিভিউ', 'azadnews-photo-card'); ?></h3>
                            <button type="button" class="button button-small" id="azad_admin_refresh_preview"><span class="dashicons dashicons-image-rotate"></span> <?php _e('রিফ্রেশ', 'azadnews-photo-card'); ?></button>
                        </div>
                        <p class="description"><?php _e('আপনার সেটিংস অনুযায়ী লাইভ ফটো কার্ড কেমন দেখাবে তা এখানে দেখে টেস্ট কার্ড ডাউনলোড করতে পারেন।', 'azadnews-photo-card'); ?></p>

                        <!-- Card Canvas Container -->
                        <div class="azad-admin-card-stage" id="azad_admin_card_container">
                            <!-- Injected by JavaScript -->
                        </div>

                        <div class="azad-admin-preview-actions">
                            <button type="button" class="button button-primary button-large azad-admin-download-btn" id="azad_admin_download_btn">
                                <span class="dashicons dashicons-download"></span> <?php _e('টেস্ট ফটো কার্ড ডাউনলোড করুন', 'azadnews-photo-card'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
