/**
 * Global Date Picker Enhancer
 * Makes entire date input field clickable, not just the icon
 */

(function($) {
    "use strict";

    const DatepickerEnhancer = function() {};

    DatepickerEnhancer.prototype.init = function() {
        this.enhanceAllDatepickers();
        this.handleDateRange();
    };

    DatepickerEnhancer.prototype.enhanceAllDatepickers = function() {
        // Select all datepicker inputs (with or without ID)
        $('input.datepicker, input[data-provide="datepicker"], #datepicker, #datepicker-autoclose, #datepicker-multiple-date').each(function() {
            const $input = $(this);
            const $parent = $input.closest('.input-group');

            // If inside an input-group, make the whole group clickable
            if ($parent.length) {
                // When clicking anywhere in the input-group (except buttons/inputs), trigger datepicker
                $parent.on('click', function(e) {
                    // Don't trigger if clicking on an input or button that's not our date input
                    if (!$(e.target).is('input') || $(e.target).is($input)) {
                        // Prevent default and show datepicker
                        e.preventDefault();
                        $input.datepicker('show');
                    }
                });

                // Also ensure the input itself is clickable (standard behavior)
                $input.on('click', function(e) {
                    e.stopPropagation(); // Prevent double trigger
                    $input.datepicker('show');
                });
            }
        });
    };

    DatepickerEnhancer.prototype.handleDateRange = function() {
        // Special handling for date range picker
        const $dateRange = $('#date-range');
        if ($dateRange.length) {
            // The date-range is already initialized with toggleActive: true
            // Make the container clickable too
            $dateRange.closest('.input-daterange').on('click', function(e) {
                if (!$(e.target).is('input')) {
                    e.preventDefault();
                    // Show picker on first input
                    $dateRange.find('input:first').datepicker('show');
                }
            });

            // Individual inputs still work normally
            $dateRange.find('input').on('click', function(e) {
                e.stopPropagation();
                $(this).datepicker('show');
            });
        }
    };

    // Initialize
    $.DatepickerEnhancer = new DatepickerEnhancer();
    $.DatepickerEnhancer.Constructor = DatepickerEnhancer;

})(window.jQuery);

// Run after page load
$(document).ready(function() {
    if (typeof $.DatepickerEnhancer !== 'undefined') {
        $.DatepickerEnhancer.init();
    }
});
