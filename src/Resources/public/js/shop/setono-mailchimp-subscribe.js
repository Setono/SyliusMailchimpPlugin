(function ($) {
    'use strict';

    $.fn.extend({
        joinNewsletter: function () {
            let form = $(this);
            form.submit(function (event) {
                event.preventDefault();

                let successElement = form.find('.success-element');
                let validationElement = form.find('.validation-element');

                successElement.text('');
                validationElement.text('');

                $.ajax({
                    url: $(form).attr('action'),
                    type: $(form).attr('method'),
                    data: form.serialize()
                })
                    .done(function (response) {
                        if (response.hasOwnProperty('message')) {
                            successElement.html(response.message);
                        }
                    })
                    .fail(function (response) {
                        if (response.responseJSON.hasOwnProperty('errors')) {
                            let errors = $.parseJSON(response.responseJSON.errors);
                            let message = '';

                            $(errors).each(function (key, value) {
                                message += value + " ";
                            });

                            validationElement.text(message);
                        }
                    });
            });
        }
    });
})(jQuery);
