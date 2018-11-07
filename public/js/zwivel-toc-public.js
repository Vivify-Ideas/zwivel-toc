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

    var topMenu = $('#zwivel-toc-container');
    var topMenuHeight = topMenu.outerHeight() + 1;
    // console.log(topMenuHeight);
      // // All list items
      // menuItems = topMenu.find("a"),
      // // Anchors corresponding to menu items
      // scrollItems = menuItems.map(function(){
      //   var item = $($(this).attr("href"));
      //   if (item.length) { return item; }
      // });

    $(window).scroll(function() {
      var fromTop = $(this).scrollTop() + topMenuHeight;
      // console.log(fromTop);
      $('.zwivel-toc-section').each(function() {
        if ($(window).scrollTop() >= $(this).offset().top) {
          console.log($(this).attr('id'));
          var id = $(this).attr('id');
          $('.ez-toc-list li a').removeClass('active_underlined');
          $('.ez-toc-list li a[href=#'+ id +']').addClass('active_underlined');
        }
      })
    });


  });

})( jQuery );
