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
        var dropdown = $('.zw-c-toc-dropdown');
        var dropdownMenu = $('.zw-c-toc-dropdown-menu');
        var dropdownCloseBtn = $('.zw-c-toc-dropdown-menu-close');
        var dropdownToggleBtn = $('.zw-c-toc-dropdown .zw-c-btn');
        var sidebarTOCItems = $('.zw-toc-list li a');
        var next;
        var previous;
        var wpadminbar = $('#wpadminbar');
        var isArticleEnd;
        var lastHTag;

        var sidebarTocHrefs = [];
        for (var i = 0; i < sidebarTOCItems.length; i++) {
            sidebarTocHrefs.push(sidebarTOCItems[i].getAttribute('data-href').substr(1));
        }

        lastHTag = sidebarTocHrefs.pop();


        /******************/
        /*   functions    */
        /******************/

        var handleScroll = function() {

            toggleStickyTOC();

            var lastPassedTocTagOffset = Number.NEGATIVE_INFINITY;
            var lastPassedTocTag = null;

            contentHTags.each(function(index, item) {
                var currentHTagId = contentHTags.eq(index).attr('id');

                if ($.inArray(currentHTagId, sidebarTocHrefs) === -1) {
                    return;
                }

                var tagOffset = $(item).offset().top - getHeaderOffset() - $(window).scrollTop();
                // added for mobile devices, without this line header changing does not work as expected
                tagOffset -= 2;

                if (tagOffset <= 0 && tagOffset > lastPassedTocTagOffset) {
                    lastPassedTocTagOffset = tagOffset;
                    lastPassedTocTag = contentHTags.eq(index);
                    next = getNext(index, sidebarTocHrefs);
                    previous = getPrevious(index, sidebarTocHrefs);
                }
            });

            setCurrentHeadingTextInStickyTOC(lastPassedTocTag);
            setNextHeadingTextInStickyTOC(next);

            if (lastPassedTocTag) {
                markCurrentItemInDropdown(lastPassedTocTag.attr('id'));
            }

            handleStickyHeaderNextButtonAppearance();
            handleStickyHeaderPreviousButtonAppearance();
        };


        function setCurrentHeadingTextInStickyTOC(lastPassedTocTag) {
            if (!lastPassedTocTag) {
                return;
            }

            var currentHeading = getCorrectTextForStickyTOC(lastPassedTocTag);
            stickyTOCHeaderTitle.text(currentHeading.text());
        }


        function setNextHeadingTextInStickyTOC(next) {
            if (!next) {
                stickyTOCHeaderNextTitle.text('');
                return;
            }

            var element = getCorrectTextForStickyTOC(next);
            stickyTOCHeaderNextTitle.text(element.text());
        }


        function getCorrectTextForStickyTOC(headingFromContent) {
            return sidebarTOCItems.filter(function(index, el) {
                return $(el).attr('data-href').substr(1) === headingFromContent.attr('id');
            }).first();
        }


        function getNext(index, ids) {
            if (index > contentHTags.length) {
                return;
            }

            if ($.inArray(contentHTags.eq(index + 1).attr('id'), sidebarTocHrefs) !== -1) {
                return contentHTags.eq(index + 1);
            }

            index++;
            return getNext(index, ids);
        }


        function getPrevious(index, ids) {
            if (index <= 0) {
                return;
            }

            if ($.inArray(contentHTags.eq(index - 1).attr('id'), sidebarTocHrefs) !== -1) {
                return $(contentHTags[index - 1]);
            }

            index--;
            return getPrevious(index, ids);
        }


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
            $('.zw-c-toc-dropdown-menu li a[data-href=#'+ id +']').addClass('zw-c-toc-dropdown-menu-current');
        }


        function handleStickyHeaderNextButtonAppearance() {
            if (!next) {
                stickyTOCHeaderNextBtn.addClass('hidden');
                stickyTOCHeaderNextHolder.text('');
                isArticleEnd = true;
            } else {
                stickyTOCHeaderNextBtn.removeClass('hidden');
                stickyTOCHeaderNextHolder.text('Next:');
                isArticleEnd = false;
            }
        }


        function handleStickyHeaderPreviousButtonAppearance() {
            if (!previous) {
                stickyTOCHeaderPreviousBtn.addClass('disabled');
            } else {
                stickyTOCHeaderPreviousBtn.removeClass('disabled');
            }
        }


        /******************/
        /* click handlers */
        /******************/
        stickyTOCHeaderPreviousBtn.click(function() {
            if (isArticleEnd) {
                scrollToID($('#' + lastHTag));
                return;
            }
            if (previous.attr('id')) {
                scrollToID(previous);
            }
        });


        stickyTOCHeaderNextBtn.click(function() {
            if (next.attr('id')) {
                scrollToID(next);
            }
        });


        sidebarTOCItems.click(function(e) {
            e.preventDefault();
            scrollToCurrentHeading($(this).attr('data-href'));
        });


        $('.zw-c-toc-dropdown-menu li:not(.zw-u-first) a').click(function(e) {
            e.preventDefault();
            scrollToCurrentHeading($(this).attr('data-href'));
            dropdown.removeClass('zw-is-active');
        });


        // mobile ****************************
        dropdownToggleBtn.click(function() {
            dropdown.addClass('zw-is-active');
        });


        dropdownCloseBtn.click(function() {
            dropdown.removeClass('zw-is-active');
        });
        //************************************


        // desktop ***************************
        dropdownToggleBtn.hover(function() {
            dropdown.addClass('zw-is-active');
        });


        dropdownMenu.mouseleave(function() {
            dropdown.removeClass('zw-is-active');
        });
        //************************************

        function getHeaderOffset() {
            var height = $('#header').height();
            height += wpadminbar.height();
            height += stickyTOCHeader.height();
            return height;
        }


        function scrollToID(item) {
            $('html, body').animate({
                scrollTop: $('#' + item.attr('id')).offset().top - getHeaderOffset()
            }, 500, function() {
                setTimeout(function() {
                    if (isArticleEnd) {
                        isArticleEnd = false;
                    }
                }, 100);
            });
            updateUrlSegment(item.attr('id'));
        }


        function scrollToCurrentHeading(currentId) {
            $('html, body').animate({
                scrollTop: $(currentId).parent().offset().top - getHeaderOffset()
            }, 500);
            updateUrlSegment(currentId);
        }

        function updateUrlSegment(hash) {
            window.location.hash = hash;
        }

        /******************/
        /* MAIN EXECUTION */
        /******************/

        getHeaderOffset();

        function initScrolling() {
            $(window).on('scroll', handleScroll);
        }

        initScrolling();

        window.onload = function() {
            handleScroll();
            if (window.location.hash) {
                scrollToCurrentHeading(window.location.hash);
            }
        };

    });

})( jQuery );