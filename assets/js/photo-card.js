/**
 * Azad News Photo Card - Frontend Script
 */

(function($) {
  'use strict';

  // Localized Post Data & Options
  var config = window.azadPhotoCardData || {
    postTitle: 'কক্সবাজারে এখনো পানির নিচে ১৫০ গ্রাম, তিন লাখের বেশি মানুষ পানিবন্দি',
    postImage: 'assets/images/sample-flood.jpg',
    postDate: '১১ জুলাই ২০২৬',
    reporterText: 'কক্সবাজার প্রতিনিধি:',
    buttonPosition: 'before_content',
    options: {
      default_title_size: 27,
      default_line_height: 1.35,
      default_bottom_size: 20,
      default_bottom_badge: 'বিস্তারিত কমেন্টে',
      footer_text: 'আজাদ নিউজ ২৪ | www.azadnews-24.com',
      logo_icon_url: 'assets/images/logo-icon.svg',
      export_scale: 2
    }
  };

  var state = {
    titleSize: parseInt((config.options && config.options.default_title_size) || 27, 10),
    lineHeight: parseFloat((config.options && config.options.default_line_height) || 1.35),
    bottomSize: parseInt((config.options && config.options.default_bottom_size) || 20, 10),
    postTitle: config.postTitle || 'কক্সবাজারে এখনো পানির নিচে ১৫০ গ্রাম, তিন লাখের বেশি মানুষ পানিবন্দি',
    postImage: config.postImage || '',
    postDate: config.postDate || '১১ জুলাই ২০২৬',
    reporterText: config.reporterText || (config.options && config.options.default_reporter_text) || 'কক্সবাজার প্রতিনিধি:',
    badgeText: (config.options && config.options.default_bottom_badge) || 'বিস্তারিত কমেন্টে',
    footerText: (config.options && config.options.footer_text) || 'আজাদ নিউজ ২৪ |',
    logoIconUrl: (config.options && config.options.logo_icon_url) || ''
  };

  /**
   * Smart Highlight for Title text (matching Bengali and English reference designs)
   */
  function formatTitleHighlight(rawTitle) {
    if (!rawTitle) return '';
    if (rawTitle.indexOf('<span') !== -1) {
      return rawTitle;
    }

    var words = rawTitle.trim().split(/\s+/);
    if (words.length <= 3) {
      return rawTitle;
    }

    var oneThird = Math.max(1, Math.floor(words.length / 3));
    var twoThird = Math.min(words.length - 1, Math.ceil((words.length * 2) / 3));

    var part1 = words.slice(0, oneThird).join(' ');
    var part2 = words.slice(oneThird, twoThird).join(' ');
    var part3 = words.slice(twoThird).join(' ');

    return part1 + ' <span class="highlight-yellow">' + part2 + '</span> ' + part3;
  }

  /**
   * Render the Photo Card markup
   */
  function renderCardDOM() {
    var $container = $('#azad_photocard_element');
    if (!$container.length) return;

    $container.css({
      '--azad-title-size': state.titleSize + 'px',
      '--azad-line-height': state.lineHeight,
      '--azad-bottom-size': state.bottomSize + 'px'
    });

    var fallbackImg = 'assets/images/sample-flood.jpg';
    var imgSrc = state.postImage ? state.postImage : fallbackImg;
    var logoIconSrc = state.logoIconUrl ? state.logoIconUrl : '';

    var formattedTitle = formatTitleHighlight(state.postTitle);

    var logoMarkup = '';
    if (logoIconSrc) {
      logoMarkup = '<img src="' + logoIconSrc + '" alt="Logo Icon" crossorigin="anonymous" />';
    } else {
      logoMarkup = '<span class="azad-icon-text">আ</span>';
    }

    var html = [
      '<!-- Top Date Bar -->',
      '<div class="azad-card-top-bar">',
        '<span class="azad-date-pill">' + state.postDate + '</span>',
      '</div>',

      '<!-- Featured Image Frame -->',
      '<div class="azad-card-image-section">',
        '<div class="azad-image-frame">',
          '<img src="' + imgSrc + '" alt="Featured Photo" crossorigin="anonymous" />',
        '</div>',
      '</div>',

      '<!-- Headline Title -->',
      '<div class="azad-card-title-section">',
        '<h2 class="azad-card-title">' + formattedTitle + '</h2>',
      '</div>',

      '<!-- Bottom Bar: Reporter + Glossy Blue Comment Badge -->',
      '<div class="azad-card-bottom-bar">',
        '<div class="azad-reporter-group">',
          '<div class="azad-logo-icon-circle">',
            logoMarkup,
          '</div>',
          '<span class="azad-reporter-name">' + state.reporterText + '</span>',
        '</div>',
        '<div class="azad-comment-badge-btn">' + state.badgeText + '</div>',
      '</div>',

      '<!-- Bottom Footer Bar -->',
      '<div class="azad-card-footer-bar">',
        '<span class="azad-footer-pill">' + state.footerText + '</span>',
      '</div>'
    ].join('');

    $container.html(html);
  }

  /**
   * Update real-time CSS variables for smooth slider adjustments
   */
  function updateVariables() {
    var $container = $('#azad_photocard_element');
    $container.css({
      '--azad-title-size': state.titleSize + 'px',
      '--azad-line-height': state.lineHeight,
      '--azad-bottom-size': state.bottomSize + 'px'
    });

    $('#val_title_size').text(state.titleSize + 'px');
    $('#val_line_height').text(state.lineHeight);
    $('#val_bottom_size').text(state.bottomSize + 'px');
  }

  /**
   * Open Modal
   */
  function openModal() {
    var $modal = $('#azad_photo_card_modal');
    if (!$modal.length) return;

    // Sync input fields
    $('#azad_input_custom_title').val(state.postTitle);
    $('#azad_input_custom_reporter').val(state.reporterText);
    $('#azad_input_custom_date').val(state.postDate);

    $('#azad_slider_title_size').val(state.titleSize);
    $('#azad_slider_line_height').val(state.lineHeight);
    $('#azad_slider_bottom_size').val(state.bottomSize);

    renderCardDOM();
    updateVariables();

    $modal.show().addClass('active');
    $('body').css('overflow', 'hidden');
  }

  /**
   * Close Modal
   */
  function closeModal() {
    var $modal = $('#azad_photo_card_modal');
    $modal.removeClass('active');
    setTimeout(function() {
      $modal.hide();
      $('body').css('overflow', '');
    }, 200);
  }

  /**
   * Export & Download Card using html2canvas
   */
  function downloadCard() {
    var $btn = $('#azad_download_card_btn');
    var $spinner = $btn.find('.azad-btn-spinner');
    var $text = $btn.find('.azad-btn-text');
    var cardElement = document.getElementById('azad_photocard_element');

    if (!cardElement) return;

    if (typeof html2canvas === 'undefined') {
      alert('html2canvas library is not loaded.');
      return;
    }

    $btn.addClass('loading').prop('disabled', true);
    $spinner.show();
    $text.text('Generating Card...');

    // Ensure Bengali web fonts are ready before drawing to canvas
    var fontPromise = (document.fonts && document.fonts.ready) ? document.fonts.ready : Promise.resolve();

    fontPromise.then(function() {
      var scaleVal = parseInt((config.options && config.options.export_scale) || 2, 10);

      html2canvas(cardElement, {
        scale: scaleVal,
        useCORS: true,
        allowTaint: true,
        logging: false,
        backgroundColor: null,
        width: 600,
        height: 600
      }).then(function(canvas) {
        try {
          var imageURI = canvas.toDataURL('image/png');
          var link = document.createElement('a');
          var cleanName = (state.postTitle || 'azadnews-photocard')
            .replace(/[^\w\s\u0980-\u09FF-]/g, '')
            .substring(0, 40)
            .trim()
            .replace(/\s+/g, '-');

          link.download = 'azadnews-' + cleanName + '.png';
          link.href = imageURI;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
        } catch (e) {
          console.error('Download error:', e);
          alert('Could not generate download image. Error: ' + e.message);
        }

        $btn.removeClass('loading').prop('disabled', false);
        $spinner.hide();
        $text.text('Download Photo Card');
      }).catch(function(err) {
        console.error('html2canvas render failed:', err);
        $btn.removeClass('loading').prop('disabled', false);
        $spinner.hide();
        $text.text('Download Photo Card');
        alert('Canvas generation failed. Please check image permissions.');
      });
    });
  }

  /**
   * Intelligently place button above post content paragraphs and below image/meta
   */
  function placeButtonAboveContent() {
    var $btnWrapper = $('.azad-photo-card-trigger-wrapper');
    if (!$btnWrapper.length) return;

    // Check if there is author / category meta bar (e.g. "Written by sarkarhost in Cardiology")
    var $meta = $('p:contains("Written by"), .entry-meta, .post-meta, .byline, .post-info, .entry-header').last();
    if ($meta.length) {
      // If the button is not directly after the meta, place it right after meta
      if ($meta.next('.azad-photo-card-trigger-wrapper').length === 0) {
        $meta.after($btnWrapper.first());
        return;
      }
    }

    // Otherwise, ensure it's at the very top of the content container
    var $contentContainer = $('.entry-content, .post-content, article .entry-content, .single-content, article .content, .single-post-content, .post-entry, article').first();
    if ($contentContainer.length) {
      if ($contentContainer.children().first()[0] !== $btnWrapper[0]) {
        $contentContainer.prepend($btnWrapper.first());
      }
    }
  }

  /**
   * DOM Ready Bindings
   */
  $(document).ready(function() {
    // Reposition button right above content / below meta and image
    placeButtonAboveContent();

    $(window).on('load', function() {
      placeButtonAboveContent();
    });

    // Open Button Trigger
    $(document).on('click', '#azad_open_photocard_btn', function(e) {
      e.preventDefault();
      openModal();
    });

    // Close Button & Backdrop
    $(document).on('click', '#azad_close_modal_btn, .azad-modal-backdrop', function(e) {
      e.preventDefault();
      closeModal();
    });

    // Esc Key Listener
    $(document).on('keydown', function(e) {
      if (e.key === 'Escape' && $('#azad_photo_card_modal').hasClass('active')) {
        closeModal();
      }
    });

    // Title Font Size Slider
    $(document).on('input change', '#azad_slider_title_size', function() {
      state.titleSize = parseInt($(this).val(), 10);
      updateVariables();
    });

    // Line Height Slider
    $(document).on('input change', '#azad_slider_line_height', function() {
      state.lineHeight = parseFloat($(this).val());
      updateVariables();
    });

    // Bottom Font Size Slider
    $(document).on('input change', '#azad_slider_bottom_size', function() {
      state.bottomSize = parseInt($(this).val(), 10);
      updateVariables();
    });

    // Quick Edit Accordion Toggle
    $(document).on('click', '#azad_toggle_quick_edit', function(e) {
      e.preventDefault();
      var $fields = $('#azad_quick_edit_fields');
      var $chevron = $(this).find('.azad-chevron');
      if ($fields.is(':visible')) {
        $fields.slideUp(200);
        $chevron.text('▼');
      } else {
        $fields.slideDown(200);
        $chevron.text('▲');
      }
    });

    // Live Title Field Change
    $(document).on('input', '#azad_input_custom_title', function() {
      state.postTitle = $(this).val();
      renderCardDOM();
    });

    // Live Reporter Field Change
    $(document).on('input', '#azad_input_custom_reporter', function() {
      state.reporterText = $(this).val();
      renderCardDOM();
    });

    // Live Date Field Change
    $(document).on('input', '#azad_input_custom_date', function() {
      state.postDate = $(this).val();
      renderCardDOM();
    });

    // Download Button Click
    $(document).on('click', '#azad_download_card_btn', function(e) {
      e.preventDefault();
      downloadCard();
    });
  });

})(jQuery);
