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

	//TESTING
	$(function() {

    var sidebarTOC = $('#zwivel-toc-container');
    var contentHTags = $('.zwivel-toc-section');
    var stickyTOCHeader = $('.zw-c-toc');
    // var stickyTOCHeaderContent = [];
    var stickyTOCHeaderTitle = $('.js-toc-title');
    var stickyTOCHeaderNextTitle = $('.zw-c-toc-chapter-next');

    // $('.zwivel-toc-section').each(function(index, tocItem) {
    //   stickyTOCHeaderContent.push(
    //     {
    //       text: $(tocItem).text(),
    //       id: $(tocItem).attr('id')
    //     }
    //   );
    // });

    // console.log(stickyTOCHeaderContent);

    $(window).scroll(function() {

      var elementTop = sidebarTOC.offset().top;
      var elementBottom = elementTop + sidebarTOC.outerHeight();
      var viewportTop = $(window).scrollTop();
      // var viewportBottom = viewportTop + $(window).height();

      if (elementBottom < viewportTop) {
        stickyTOCHeader.css('display', 'block');
      } else {
        stickyTOCHeader.css('display', 'none');
      }

      contentHTags.each(function(index, item) {
        if ($(window).scrollTop() >= $(item).offset().top) {
          // var id = $(this).attr('id');
          // $('.ez-toc-list li a').removeClass('active_underlined');
          // $('.ez-toc-list li a[href=#'+ id +']').addClass('active_underlined');

          stickyTOCHeaderTitle.text($(item).text());
          stickyTOCHeaderNextTitle.text(contentHTags.eq(index + 1).text());

        }
      })
    });

  });

})( jQuery );
