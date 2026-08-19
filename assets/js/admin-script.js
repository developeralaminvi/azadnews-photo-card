/**
 * Azad News Photo Card - Admin Settings JavaScript
 */

(function($) {
  'use strict';

  var adminData = window.azadPhotoCardAdmin || {};
  var opts = adminData.options || {};

  var adminState = {
    titleSize: parseInt(opts.default_title_size, 10) || 27,
    lineHeight: parseFloat(opts.default_line_height) || 1.35,
    bottomSize: parseInt(opts.default_bottom_size, 10) || 20,
    fontFamily: opts.default_font_family || "'Hind Siliguri', sans-serif",
    postTitle: 'কক্সবাজারে এখনো পানির নিচে ১৫০ গ্রাম, তিন লাখের বেশি মানুষ পানিবন্দি',
    postDate: adminData.bengaliDate || '১১ জুলাই ২০২৬',
    reporterText: opts.default_reporter_text || 'কক্সবাজার প্রতিনিধি:',
    badgeText: opts.default_bottom_badge || 'বিস্তারিত কমেন্টে',
    footerText: opts.footer_text || 'আজাদ নিউজ ২৪ | www.azadnews-24.com',
    logoIconUrl: opts.logo_icon_url || 'assets/images/logo-icon.svg',
    sampleImage: 'assets/images/sample-flood.jpg'
  };

  /**
   * Helper to format title with yellow highlight
   */
  function formatTitleHighlight(rawTitle) {
    if (!rawTitle) return '';
    var words = rawTitle.trim().split(/\s+/);
    if (words.length <= 3) return rawTitle;
    var oneThird = Math.max(1, Math.floor(words.length / 3));
    var twoThird = Math.min(words.length - 1, Math.ceil((words.length * 2) / 3));

    var part1 = words.slice(0, oneThird).join(' ');
    var part2 = words.slice(oneThird, twoThird).join(' ');
    var part3 = words.slice(twoThird).join(' ');

    return part1 + ' <span class="highlight-yellow">' + part2 + '</span> ' + part3;
  }

  /**
   * Render Live Card Preview in Admin Dashboard
   */
  function renderAdminPreview() {
    var $stage = $('#azad_admin_card_container');
    if (!$stage.length) return;

    var formattedTitle = formatTitleHighlight(adminState.postTitle);
    var logoMarkup = adminState.logoIconUrl ? '<img src="' + adminState.logoIconUrl + '" alt="Icon" />' : '<span class="azad-icon-text">আ</span>';

    var html = '<div id="azad_admin_preview_card" class="azad-photocard-container" style="--azad-title-size:' + adminState.titleSize + 'px; --azad-line-height:' + adminState.lineHeight + '; --azad-bottom-size:' + adminState.bottomSize + 'px; font-family:' + adminState.fontFamily + ';">';

    html += [
      '<div class="azad-card-top-bar">',
        '<span class="azad-date-pill">' + adminState.postDate + '</span>',
      '</div>',
      '<div class="azad-card-image-section">',
        '<div class="azad-image-frame">',
          '<img src="' + adminState.sampleImage + '" alt="Sample Photo" />',
        '</div>',
      '</div>',
      '<div class="azad-card-title-section">',
        '<h2 class="azad-card-title">' + formattedTitle + '</h2>',
      '</div>',
      '<div class="azad-card-bottom-bar">',
        '<div class="azad-reporter-group">',
          '<div class="azad-logo-icon-circle">',
            logoMarkup,
          '</div>',
          '<span class="azad-reporter-name">' + adminState.reporterText + '</span>',
        '</div>',
        '<div class="azad-comment-badge-btn">' + adminState.badgeText + '</div>',
      '</div>',
      '<div class="azad-card-footer-bar">',
        '<span class="azad-footer-pill">' + adminState.footerText + '</span>',
      '</div>'
    ].join('');

    html += '</div>';
    $stage.html(html);
  }

  $(document).ready(function() {
    // Tab switching
    $('.azad-nav-tabs .nav-tab').on('click', function(e) {
      e.preventDefault();
      $('.azad-nav-tabs .nav-tab').removeClass('nav-tab-active');
      $(this).addClass('nav-tab-active');
      var target = $(this).data('tab');
      $('.azad-tab-content').removeClass('azad-tab-active');
      $('#' + target).addClass('azad-tab-active');
    });

    // WP Media Uploader trigger
    $(document).on('click', '.azad-upload-btn', function(e) {
      e.preventDefault();
      var $btn = $(this);
      var targetInput = $btn.data('target');
      var previewTarget = $btn.data('preview');

      if (typeof wp === 'undefined' || !wp.media) {
        return;
      }

      var customUploader = wp.media({
        title: (adminData.i18n && adminData.i18n.chooseImage) || 'লোগো আইকন নির্বাচন করুন',
        button: {
          text: (adminData.i18n && adminData.i18n.useThisImage) || 'এই ছবি ব্যবহার করুন'
        },
        multiple: false
      });

      customUploader.on('select', function() {
        var attachment = customUploader.state().get('selection').first().toJSON();
        $(targetInput).val(attachment.url).trigger('change');
        if (previewTarget) {
          $(previewTarget).attr('src', attachment.url);
        }
      });

      customUploader.open();
    });

    // Sync Sliders & Numbers
    $('#range_title_size').on('input change', function() {
      var val = $(this).val();
      $('#azad_default_title_size').val(val);
      $('#admin_val_title').text(val + 'px');
      adminState.titleSize = parseInt(val, 10);
      renderAdminPreview();
    });
    $('#azad_default_title_size').on('input change', function() {
      var val = $(this).val();
      $('#range_title_size').val(val);
      $('#admin_val_title').text(val + 'px');
      adminState.titleSize = parseInt(val, 10);
      renderAdminPreview();
    });

    $('#range_line_height').on('input change', function() {
      var val = $(this).val();
      $('#azad_default_line_height').val(val);
      $('#admin_val_line').text(val);
      adminState.lineHeight = parseFloat(val);
      renderAdminPreview();
    });
    $('#azad_default_line_height').on('input change', function() {
      var val = $(this).val();
      $('#range_line_height').val(val);
      $('#admin_val_line').text(val);
      adminState.lineHeight = parseFloat(val);
      renderAdminPreview();
    });

    $('#range_bottom_size').on('input change', function() {
      var val = $(this).val();
      $('#azad_default_bottom_size').val(val);
      adminState.bottomSize = parseInt(val, 10);
      renderAdminPreview();
    });
    $('#azad_default_bottom_size').on('input change', function() {
      var val = $(this).val();
      $('#range_bottom_size').val(val);
      adminState.bottomSize = parseInt(val, 10);
      renderAdminPreview();
    });

    $('#azad_default_font_family').on('change', function() {
      adminState.fontFamily = $(this).val();
      renderAdminPreview();
    });

    $('#azad_logo_icon_url').on('input change', function() {
      adminState.logoIconUrl = $(this).val();
      $('#azad_logo_icon_preview').attr('src', adminState.logoIconUrl);
      renderAdminPreview();
    });

    $('#azad_footer_text').on('input', function() {
      adminState.footerText = $(this).val();
      renderAdminPreview();
    });

    $('#azad_default_bottom_badge').on('input', function() {
      adminState.badgeText = $(this).val();
      renderAdminPreview();
    });

    $('#azad_default_reporter_text').on('input', function() {
      adminState.reporterText = $(this).val();
      renderAdminPreview();
    });

    $('#azad_admin_refresh_preview').on('click', function(e) {
      e.preventDefault();
      renderAdminPreview();
    });

    // Download Test Button from Admin
    $('#azad_admin_download_btn').on('click', function(e) {
      e.preventDefault();
      var card = document.getElementById('azad_admin_preview_card');
      if (!card || typeof html2canvas === 'undefined') return;

      var $btn = $(this);
      $btn.prop('disabled', true).text((adminData.i18n && adminData.i18n.downloading) || 'ডাউনলোড হচ্ছে...');

      html2canvas(card, {
        scale: 2,
        useCORS: true,
        allowTaint: true,
        logging: false,
        backgroundColor: null
      }).then(function(canvas) {
        var link = document.createElement('a');
        link.download = 'azadnews-test-card.png';
        link.href = canvas.toDataURL('image/png');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        $btn.prop('disabled', false).html('<span class="dashicons dashicons-download"></span> ' + ((adminData.i18n && adminData.i18n.download) || 'টেস্ট ফটো কার্ড ডাউনলোড করুন'));
      }).catch(function(err) {
        console.error(err);
        $btn.prop('disabled', false).html('<span class="dashicons dashicons-download"></span> টেস্ট ফটো কার্ড ডাউনলোড করুন');
      });
    });

    // Initial render
    renderAdminPreview();
  });

})(jQuery);
