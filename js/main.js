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
        $('.stats-table').each(function() {
            var table = $(this);
            table.find('th').addClass('sortable-header').attr('tabindex', '0');
        });

        $('.stats-table').on('click keydown', 'th', function(event) {
            if (event.type === 'keydown' && event.key !== 'Enter' && event.key !== ' ') {
                return;
            }

            event.preventDefault();

            var header = $(this);
            var table = header.closest('table');
            var tbody = table.find('tbody').first();
            var columnIndex = header.index();
            var direction = header.hasClass('sorted-asc') ? 'desc' : 'asc';
            var rows = tbody.find('tr').toArray();

            table.find('th')
                .removeClass('sorted-asc sorted-desc')
                .attr('aria-sort', 'none');

            header
                .addClass(direction === 'asc' ? 'sorted-asc' : 'sorted-desc')
                .attr('aria-sort', direction === 'asc' ? 'ascending' : 'descending');

            rows.sort(function(rowA, rowB) {
                var valueA = getCellSortValue(rowA, columnIndex);
                var valueB = getCellSortValue(rowB, columnIndex);
                var result = compareSortValues(valueA, valueB);

                return direction === 'asc' ? result : result * -1;
            });

            tbody.append(rows);
        });

        function compareSortValues(valueA, valueB) {
            var numberA = parseSortNumber(valueA);
            var numberB = parseSortNumber(valueB);

            if (!isNaN(numberA) && !isNaN(numberB)) {
                return numberA - numberB;
            }

            return valueA.toString().localeCompare(valueB.toString(), undefined, {
                numeric: true,
                sensitivity: 'base'
            });
        }

        function parseSortNumber(value) {
            if (typeof value !== 'string') {
                return NaN;
            }

            return parseFloat(value.replace(/,/g, ''));
        }

        function getCellSortValue(row, index) {
            var cell = $(row).children('td').eq(index);
            var value = cell.attr('data-value');

            return typeof value !== 'undefined' ? value.trim() : cell.text().trim();
        }
    });

})(jQuery);
