<?php
/**
 * Plugin Name: Azad News Photo Card
 * Plugin URI: https://azadnews-24.com/
 * Description: Azad News-24 এর জন্য প্রফেশনাল ফটো কার্ড জেনারেটর প্লাগইন। সিঙ্গেল পোস্ট থেকে সরাসরি সোশ্যাল মিডিয়া রেডি ফটো কার্ড তৈরি ও ডাউনলোড করুন।
 * Version: 1.0.3
 * Author: Azad News 24 Team
 * Author URI: https://azadnews-24.com/
 * Text Domain: azadnews-photo-card
 * Domain Path: /languages
 * License: GPL-2.0+
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('AZAD_PHOTO_CARD_VERSION', '1.0.3');
define('AZAD_PHOTO_CARD_PATH', plugin_dir_path(__FILE__));
define('AZAD_PHOTO_CARD_URL', plugin_dir_url(__FILE__));
define('AZAD_PHOTO_CARD_BASENAME', plugin_basename(__FILE__));

// Require Core Classes
require_once AZAD_PHOTO_CARD_PATH . 'includes/class-bengali-date.php';
require_once AZAD_PHOTO_CARD_PATH . 'includes/class-settings.php';
require_once AZAD_PHOTO_CARD_PATH . 'includes/class-frontend.php';

/**
 * Main Plugin Class
 */
final class Azad_News_Photo_Card {

    /**
     * Plugin instance.
     * @var Azad_News_Photo_Card
     */
    private static $instance = null;

    /**
     * Settings instance.
     * @var Azad_Photo_Card_Settings
     */
    public $settings;

    /**
     * Frontend instance.
     * @var Azad_Photo_Card_Frontend
     */
    public $frontend;

    /**
     * Main instance getter.
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor.
     */
    private function __construct() {
        $this->init_components();
        add_action('plugins_loaded', array($this, 'load_textdomain'));
        add_action('init', array($this, 'check_and_update_default_position'));
        register_activation_hook(__FILE__, array($this, 'on_activation'));
    }

    /**
     * Initialize plugin components.
     */
    private function init_components() {
        $this->settings = new Azad_Photo_Card_Settings();
        $this->frontend = new Azad_Photo_Card_Frontend();
    }

    /**
     * Ensure button position is always set to before_content above text
     */
    public function check_and_update_default_position() {
        $opts = get_option('azad_photo_card_options');
        if (is_array($opts) && isset($opts['button_position']) && $opts['button_position'] === 'after_content') {
            $opts['button_position'] = 'before_content';
            update_option('azad_photo_card_options', $opts);
        }
    }

    /**
     * Load plugin translations.
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'azadnews-photo-card',
            false,
            dirname(AZAD_PHOTO_CARD_BASENAME) . '/languages/'
        );
    }

    /**
     * Plugin activation default options.
     */
    public function on_activation() {
        $default_options = array(
            'logo_icon_url'         => AZAD_PHOTO_CARD_URL . 'assets/images/logo-icon.svg',
            'footer_text'           => 'আজাদ নিউজ ২৪ | www.azadnews-24.com',
            'default_title_size'    => 27,
            'default_line_height'   => 1.35,
            'default_bottom_size'   => 20,
            'default_font_family'   => "'Hind Siliguri', sans-serif",
            'default_bottom_badge'  => 'বিস্তারিত কমেন্টে',
            'default_reporter_mode' => 'custom',
            'default_reporter_text' => 'কক্সবাজার প্রতিনিধি:',
            'button_position'       => 'before_content',
            'button_text'           => 'Photo Card',
            'export_scale'          => 2,
            'user_access'           => 'all',
        );

        update_option('azad_photo_card_options', $default_options);
    }
}

/**
 * Bootstrap function.
 */
function azad_news_photo_card() {
    return Azad_News_Photo_Card::get_instance();
}

// Start the plugin.
azad_news_photo_card();
