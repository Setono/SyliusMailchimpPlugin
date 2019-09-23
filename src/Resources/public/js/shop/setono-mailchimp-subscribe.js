(function ($) {
  'use strict';

  $.fn.extend({
    subscribeToNewsletter: function () {
      let $form = $(this);
      $form.on('submit', function (event) {
        event.preventDefault();

        let $status = $form.find('.setono-mailchimp-status');
        $status.removeClass('negative').removeClass('positive').empty().hide();

        $.ajax({
          url: $form.attr('action'),
          type: $form.attr('method'),
          data: $form.serialize()
        })
          .done(function (response) {
            $status.text(response.message).addClass('positive').show();
          })
          .fail(function (response) {
            $status.text(response.responseJSON.message).addClass('negative').show();
          });
      });
    }
  });
})(jQuery);
