(function ($) {
    'use strict';

    $.fn.extend({
        exportToMailChimp: function () {
            var element = $(this);

            element.click(function (event) {
                event.preventDefault();
                element.addClass('disabled');

                var href = $(element).attr('href');

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
