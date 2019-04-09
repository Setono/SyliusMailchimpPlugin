(function ($) {
    'use strict';

    $.fn.extend({
        exportToMailchimp: function () {
            let element = $(this);

            element.on('click', function (event) {
                event.preventDefault();
                element.addClass('disabled');

                let href = $(element).closest('form').attr('action');

                $.ajax({
                    method: 'POST',
                    url: href,
                    success: function (response) {
                        if (undefined !== response.redirect) {
                            window.location.href = response.redirect;

                            return;
                        }

                        window.location.reload();

                        element.removeClass('disabled');
                    },
                    error: function () {
                        window.location.reload();

                        element.removeClass('disabled');
                    }
                });
            });
        }
    })
})(jQuery);
