/**
 * Baseball Stats Theme JavaScript
 *
 * @package Baseball_Stats
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        // Mobile menu toggle
        $('.menu-toggle').on('click', function() {
            $('.main-navigation').toggleClass('active');
        });

        // Smooth scroll for anchor links
        $('a[href^="#"]').on('click', function(e) {
            var target = $(this.getAttribute('href'));
            if (target.length) {
                e.preventDefault();
                $('html, body').stop().animate({
                    scrollTop: target.offset().top - 100
                }, 1000);
            }
        });

        // Add animation on scroll
        function animateOnScroll() {
            $('.stats-card, .player-card').each(function() {
                var elementTop = $(this).offset().top;
                var elementBottom = elementTop + $(this).outerHeight();
                var viewportTop = $(window).scrollTop();
                var viewportBottom = viewportTop + $(window).height();

                if (elementBottom > viewportTop && elementTop < viewportBottom) {
                    $(this).addClass('animate-in');
                }
            });
        }

        $(window).on('scroll', animateOnScroll);
        animateOnScroll(); // Initial check

        // Auto-calculate batting average
        $('#hits, #at_bats').on('change', function() {
            var hits = parseFloat($('#hits').val()) || 0;
            var atBats = parseFloat($('#at_bats').val()) || 0;
            
            if (atBats > 0) {
                var avg = (hits / atBats).toFixed(3);
                $('#batting_avg').val(avg);
            }
        });

        // Table sorting
        $('.stats-table th').on('click', function() {
            var table = $(this).parents('table').eq(0);
            var rows = table.find('tr:gt(0)').toArray().sort(comparer($(this).index()));
            this.asc = !this.asc;
            if (!this.asc) {
                rows = rows.reverse();
            }
            for (var i = 0; i < rows.length; i++) {
                table.append(rows[i]);
            }
        });

        function comparer(index) {
            return function(a, b) {
                var valA = getCellValue(a, index);
                var valB = getCellValue(b, index);
                return $.isNumeric(valA) && $.isNumeric(valB) ? 
                    valA - valB : valA.toString().localeCompare(valB);
            };
        }

        function getCellValue(row, index) {
            return $(row).children('td').eq(index).text();
        }
    });

})(jQuery);
