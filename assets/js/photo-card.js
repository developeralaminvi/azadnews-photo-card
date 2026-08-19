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
   * Smart Highlight for Title text
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
        '<div class="azad-image-frame" style="background-image: url(\'' + imgSrc + '\');">',
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
   * Dynamic Responsive Preview Scaling for Any Device
   */
  function adjustPreviewScale() {
    var $modal = $('#azad_photo_card_modal');
    if (!$modal.hasClass('active')) return;

    var $previewArea = $('.azad-preview-area');
    var $wrapper = $('.azad-card-wrapper');
    if (!$previewArea.length || !$wrapper.length) return;

    var availableWidth = $previewArea.width();
    if (!availableWidth || availableWidth <= 0) {
      availableWidth = $(window).width() - 40;
    }

    var baseSize = 600;
    var scale = Math.min(1, availableWidth / baseSize);

    if (scale < 1) {
      $wrapper.css({
        'transform': 'scale(' + scale + ')',
        'transform-origin': 'top center',
        'width': baseSize + 'px',
        'height': (baseSize * scale) + 'px',
        'margin-bottom': '0'
      });
      $previewArea.css('min-height', (baseSize * scale + 10) + 'px');
    } else {
      $wrapper.css({
        'transform': 'none',
        'width': baseSize + 'px',
        'height': baseSize + 'px',
        'margin-bottom': '0'
      });
      $previewArea.css('min-height', 'auto');
    }
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

    // Trigger dynamic scaling on open
    setTimeout(function() {
      adjustPreviewScale();
    }, 50);
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
   * Export & Download Card using Isolated Offscreen Canvas
   */
  function downloadCard() {
    var $btn = $('#azad_download_card_btn');
    var $spinner = $btn.find('.azad-btn-spinner');
    var $text = $btn.find('.azad-btn-text');
    var sourceCard = document.getElementById('azad_photocard_element');

    if (!sourceCard) return;

    if (typeof html2canvas === 'undefined') {
      alert('html2canvas library is not loaded.');
      return;
    }

    $btn.addClass('loading').prop('disabled', true);
    $spinner.show();
    $text.text('কার্ড প্রস্তুত হচ্ছে...');

    // 1. Create a detached offscreen container at exact 600x600, untransformed
    var offscreenContainer = document.createElement('div');
    offscreenContainer.id = 'azad_offscreen_export_wrap';
    offscreenContainer.style.cssText = [
      'position: fixed',
      'left: -99999px',
      'top: 0',
      'width: 600px',
      'height: 600px',
      'min-width: 600px',
      'min-height: 600px',
      'max-width: 600px',
      'max-height: 600px',
      'overflow: hidden',
      'margin: 0',
      'padding: 0',
      'transform: none !important',
      'z-index: -9999',
      'opacity: 1',
      'pointer-events: none',
      'box-sizing: border-box',
      'background: #0b1e4f'
    ].join(';');

    // 2. Clone the photo card
    var exportClone = sourceCard.cloneNode(true);
    exportClone.id = 'azad_photocard_clone_for_export';
    exportClone.style.cssText = [
      'width: 600px !important',
      'height: 600px !important',
      'min-width: 600px !important',
      'min-height: 600px !important',
      'max-width: 600px !important',
      'max-height: 600px !important',
      'transform: none !important',
      'margin: 0 !important',
      'padding: 0 !important',
      'box-sizing: border-box !important',
      '--azad-title-size: ' + state.titleSize + 'px',
      '--azad-line-height: ' + state.lineHeight,
      '--azad-bottom-size: ' + state.bottomSize + 'px'
    ].join(';');

    // In the clone, ensure image frame uses background-image and hides img to fix html2canvas object-fit squish bug
    var clonedImgFrame = exportClone.querySelector('.azad-image-frame');
    if (clonedImgFrame) {
      var clonedImg = clonedImgFrame.querySelector('img');
      var imgSrc = (clonedImg && clonedImg.src) ? clonedImg.src : state.postImage;
      if (imgSrc) {
        clonedImgFrame.style.backgroundImage = 'url("' + imgSrc + '")';
        clonedImgFrame.style.backgroundSize = 'cover';
        clonedImgFrame.style.backgroundPosition = 'center center';
        clonedImgFrame.style.backgroundRepeat = 'no-repeat';
      }
      if (clonedImg) {
        clonedImg.style.display = 'none';
      }
    }

    offscreenContainer.appendChild(exportClone);
    document.body.appendChild(offscreenContainer);

    // Wait for fonts to be ready
    var fontPromise = (document.fonts && document.fonts.ready) ? document.fonts.ready : Promise.resolve();

    // Wait for images in the clone
    var images = exportClone.getElementsByTagName('img');
    var imagePromises = [];
    for (var i = 0; i < images.length; i++) {
      if (images[i].style.display !== 'none' && !images[i].complete) {
        imagePromises.push(new Promise(function(resolve) {
          images[i].onload = resolve;
          images[i].onerror = resolve;
        }));
      }
    }

    Promise.all([fontPromise].concat(imagePromises)).then(function() {
      setTimeout(function() {
        var scaleVal = parseInt((config.options && config.options.export_scale) || 2, 10);
        if (scaleVal < 1 || scaleVal > 3) scaleVal = 2;

        html2canvas(exportClone, {
          scale: scaleVal,
          useCORS: true,
          allowTaint: false,
          logging: false,
          backgroundColor: '#0b1e4f',
          width: 600,
          height: 600,
          windowWidth: 600,
          windowHeight: 600,
          scrollX: 0,
          scrollY: 0,
          x: 0,
          y: 0
        }).then(function(canvas) {
          try {
            var imageURI = canvas.toDataURL('image/png');
            var link = document.createElement('a');
            var cleanName = (state.postTitle || 'azadnews-photocard')
              .replace(/[^\w\s\u0980-\u09FF-]/g, '')
              .substring(0, 40)
              .trim()
              .replace(/\s+/g, '-');

            link.download = 'azadnews-' + (cleanName || 'photocard') + '.png';
            link.href = imageURI;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
          } catch (e) {
            console.error('Download error:', e);
            alert('Could not generate download image. Error: ' + e.message);
          }

          if (offscreenContainer.parentNode) {
            offscreenContainer.parentNode.removeChild(offscreenContainer);
          }
          $btn.removeClass('loading').prop('disabled', false);
          $spinner.hide();
          $text.text('Download Photo Card');
        }).catch(function(err) {
          console.error('html2canvas render failed:', err);
          if (offscreenContainer.parentNode) {
            offscreenContainer.parentNode.removeChild(offscreenContainer);
          }
          $btn.removeClass('loading').prop('disabled', false);
          $spinner.hide();
          $text.text('Download Photo Card');
          alert('ফটো কার্ড তৈরিতে সমস্যা হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন।');
        });
      }, 60);
    });
  }

  /**
   * Intelligently place button above post content paragraphs and below image/meta
   */
  function placeButtonAboveContent() {
    var $btnWrapper = $('.azad-photo-card-trigger-wrapper');
    if (!$btnWrapper.length) return;

    var $meta = $('p:contains("Written by"), .entry-meta, .post-meta, .byline, .post-info, .entry-header').last();
    if ($meta.length) {
      if ($meta.next('.azad-photo-card-trigger-wrapper').length === 0) {
        $meta.after($btnWrapper.first());
        return;
      }
    }

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
    placeButtonAboveContent();

    $(window).on('load', function() {
      placeButtonAboveContent();
    });

    $(window).on('resize orientationchange', function() {
      adjustPreviewScale();
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

