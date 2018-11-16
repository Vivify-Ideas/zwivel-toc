(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(function() {

    var sidebarTOC = $('#zwivel-toc-container');
    var contentHTags = $('.zwivel-toc-section');
    var stickyTOCHeader = $('.zw-c-toc');
    var stickyTOCHeaderTitle = $('.js-toc-title');
    var stickyTOCHeaderNextHolder = $('.zw-c-toc-chapter');
    var stickyTOCHeaderNextTitle = $('.zw-c-toc-chapter-next');
    var stickyTOCHeaderNextBtn = $('.zw-c-btn-next');
    var stickyTOCHeaderPreviousBtn = $('.zw-c-btn-previous');
    var headerOffset = 150;
    var nextId;
    var previousId;


    $(window).scroll(function() {
      toggleStickyTOC();

      contentHTags.each(function(index, item) {
        if ($(window).scrollTop() >= $(item).offset().top - headerOffset) {

          nextId = contentHTags.eq(index + 1).attr('id');
          previousId = contentHTags.eq(index - 1).attr('id');

          var id = $(this).attr('id');
          markCurrentItemInDropdown(id);

          setCurrentAndNextTitleInStickyTOC(index, item);
          handleStickyHeaderNextButtonAppearance();
        }
      })
    });


    function toggleStickyTOC() {
      var elementTop = sidebarTOC.offset().top;
      var elementBottom = elementTop + sidebarTOC.outerHeight();
      var viewportTop = $(window).scrollTop();

      if (elementBottom < viewportTop) {
        stickyTOCHeader.css('display', 'block');
      } else {
        stickyTOCHeader.css('display', 'none');
      }
    }

    function markCurrentItemInDropdown(id) {
      $('.zw-c-toc-dropdown-menu li a').removeClass('zw-c-toc-dropdown-menu-current');
      $('.zw-c-toc-dropdown-menu li a[href=#'+ id +']').addClass('zw-c-toc-dropdown-menu-current');
    }

    function setCurrentAndNextTitleInStickyTOC(index, item) {
      stickyTOCHeaderTitle.text($(item).text());
      stickyTOCHeaderNextTitle.text(contentHTags.eq(index + 1).text());
    }

    function handleStickyHeaderNextButtonAppearance() {
      if (!nextId) {
        stickyTOCHeaderNextBtn.addClass('disabled');
        stickyTOCHeaderNextHolder.text('');
      } else {
        stickyTOCHeaderNextBtn.removeClass('disabled');
        stickyTOCHeaderNextHolder.text('Next:');
      }
    }


    /******************/
    /* click handlers */
    /******************/
    stickyTOCHeaderPreviousBtn.click(function() {
      scrollToID(previousId);
    });

    stickyTOCHeaderNextBtn.click(function() {
      if (nextId) {
        scrollToID(nextId)
      }
    });

    $('.zw-toc-list li a').click(function(e) {
      e.preventDefault();
      var currentId = $(this).attr('href');
      scrollToCurrentHeading(currentId);
    });

    $('.zw-c-toc-dropdown-menu li:not(.zw-u-first) a').click(function(e) {
      e.preventDefault();
      var currentId = $(this).attr('href');
      scrollToCurrentHeading(currentId);
    });



    function scrollToID(id) {
      $('html, body').animate({
        scrollTop: $('#' + id).offset().top - headerOffset + 1
      }, 500);
    }

    function scrollToCurrentHeading(currentId) {
      $('html, body').animate({
        scrollTop: $(currentId).offset().top - headerOffset + 1
      }, 500);
    }

  });

})( jQuery );
