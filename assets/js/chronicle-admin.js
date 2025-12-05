(function ($) {
    $(function () {
        const allDayCheckbox = $('#chr_event_all_day');
        const timeFields = $('.chr-event-time');

        function toggleTimeFields() {
            if (!allDayCheckbox.length) {
                return;
            }

            if (allDayCheckbox.is(':checked')) {
                timeFields.hide();
            } else {
                timeFields.show();
            }
        }

        allDayCheckbox.on('change', toggleTimeFields);

        toggleTimeFields();
    });
})(jQuery);
