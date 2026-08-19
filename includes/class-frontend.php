<?php
/**
 * Frontend Controller for Azad News Photo Card
 */

if (!defined('ABSPATH')) {
    exit;
}

class Azad_Photo_Card_Frontend {

    /**
     * Options cache
     */
    private $options;

    /**
     * Constructor
     */
    public function __construct() {
        $this->options = Azad_Photo_Card_Settings::get_options();

        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        // High priority (5) to ensure it runs before other filters and stays at the very top of content
        add_filter('the_content', array($this, 'filter_content_button'), 5);
        add_shortcode('azad_photo_card', array($this, 'render_shortcode_button'));
        add_action('wp_footer', array($this, 'render_modal_in_footer'));
    }

    /**
     * Check if user is allowed to access the photo card generator
     */
    public function can_user_access() {
        $access = $this->options['user_access'] ?? 'all';
        if ($access === 'all') {
            return true;
        }
        if ($access === 'logged_in' && is_user_logged_in()) {
            return true;
        }
        if ($access === 'admins_only' && current_user_can('edit_posts')) {
            return true;
        }
        return false;
    }

    /**
     * Enqueue frontend scripts & styles
     */
    public function enqueue_frontend_assets() {
        if (!is_singular('post') && !is_singular() && !is_page()) {
            return;
        }

        if (!$this->can_user_access()) {
            return;
        }

        // Google Bengali Fonts
        wp_enqueue_style(
            'azad-google-fonts',
            'https://fonts.googleapis.com/css2?family=Anek+Bangla:wght@400;600;700;800&family=Hind+Siliguri:wght@400;500;600;700;800&family=Noto+Serif+Bengali:wght@600;700;900&family=Tiro+Bangla&display=swap',
            array(),
            null
        );

        // Template CSS
        wp_enqueue_style(
            'azad-templates-css',
            AZAD_PHOTO_CARD_URL . 'assets/css/templates.css',
            array(),
            AZAD_PHOTO_CARD_VERSION
        );

        // Frontend modal & UI CSS
        wp_enqueue_style(
            'azad-frontend-css',
            AZAD_PHOTO_CARD_URL . 'assets/css/frontend-style.css',
            array(),
            AZAD_PHOTO_CARD_VERSION
        );

        // html2canvas library
        wp_enqueue_script(
            'azad-html2canvas',
            AZAD_PHOTO_CARD_URL . 'assets/js/html2canvas.min.js',
            array(),
            '1.4.1',
            true
        );

        // Photo card JS
        wp_enqueue_script(
            'azad-photo-card-js',
            AZAD_PHOTO_CARD_URL . 'assets/js/photo-card.js',
            array('jquery', 'azad-html2canvas'),
            AZAD_PHOTO_CARD_VERSION,
            true
        );

        // Prepare post data
        global $post;
        $post_id = $post ? $post->ID : 0;
        $post_title = $post ? get_the_title($post_id) : '';
        $featured_img_url = '';

        if ($post_id && has_post_thumbnail($post_id)) {
            $featured_img_url = get_the_post_thumbnail_url($post_id, 'large');
        }

        // Bengali Date
        $post_time = $post ? get_the_time('U', $post_id) : current_time('timestamp');
        $bengali_date = Azad_Bengali_Date::get_bengali_date($post_time);

        // Reporter calculation
        $reporter_text = $this->options['default_reporter_text'] ?? 'কক্সবাজার প্রতিনিধি:';
        if (($this->options['default_reporter_mode'] ?? 'custom') === 'author' && $post) {
            $author_name = get_the_author_meta('display_name', $post->post_author);
            $reporter_text = $author_name ? $author_name . ':' : $reporter_text;
        }

        wp_localize_script('azad-photo-card-js', 'azadPhotoCardData', array(
            'ajaxUrl'           => admin_url('admin-ajax.php'),
            'postId'            => $post_id,
            'postTitle'         => $post_title,
            'postImage'         => $featured_img_url,
            'postDate'          => $bengali_date,
            'reporterText'      => $reporter_text,
            'buttonPosition'    => 'before_content',
            'options'           => $this->options,
            'i18n'              => array(
                'downloading'   => 'কার্ড তৈরি হচ্ছে...',
                'downloadBtn'   => 'Download Photo Card',
                'noImageNotice' => 'Featured image না থাকলে আগে post এ featured image দিন।',
                'success'       => 'ফটো কার্ড সফলভাবে ডাউনলোড হয়েছে!',
            )
        ));
    }

    /**
     * Render the Photo Card trigger button HTML
     */
    public function render_button_html() {
        if (!$this->can_user_access()) {
            return '';
        }

        $btn_text = !empty($this->options['button_text']) ? esc_html($this->options['button_text']) : 'Photo Card';

        ob_start();
        ?>
        <div class="azad-photo-card-trigger-wrapper">
            <button type="button" class="azad-photo-card-btn" id="azad_open_photocard_btn">
                <svg class="azad-btn-icon" viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                    <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                </svg>
                <span><?php echo $btn_text; ?></span>
            </button>
        </div>
        <?php
        return ob_get_clean();
    }

    /**
     * Filter post content to ALWAYS insert button ABOVE the content (below image & meta)
     */
    public function filter_content_button($content) {
        if (!is_singular('post') || !in_the_loop() || !is_main_query()) {
            return $content;
        }

        $pos = $this->options['button_position'] ?? 'before_content';
        if ($pos === 'shortcode_only') {
            return $content;
        }

        // Prevent double injection if called multiple times
        static $already_injected = array();
        global $post;
        $id = $post ? $post->ID : 0;
        if (isset($already_injected[$id])) {
            return $content;
        }
        $already_injected[$id] = true;

        $btn_html = $this->render_button_html();

        // ALWAYS PREPEND at top of content (right under image and meta)
        return $btn_html . $content;
    }

    /**
     * Shortcode [azad_photo_card]
     */
    public function render_shortcode_button($atts) {
        return $this->render_button_html();
    }

    /**
     * Render Modal Markup in Footer
     */
    public function render_modal_in_footer() {
        if (!is_singular('post') && !is_singular() && !is_page()) {
            return;
        }

        if (!$this->can_user_access()) {
            return;
        }

        require_once AZAD_PHOTO_CARD_PATH . 'templates/modal-popup.php';
    }
}
