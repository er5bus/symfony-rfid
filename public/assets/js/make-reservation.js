(function(window, $, alertify) {
    window.MakeReservation = function() {

        $(document).on(
            'click',
            '.js-make-reservation',
            this.makeReservation.bind(this)
        );

    };

    $.extend(window.MakeReservation.prototype, {
        makeReservation: function(event) {
            let $form = $(event.currentTarget).closest('form');

            $.ajax({
                url: $form.attr('action'),
                method: $form.attr('method'),
                data: $form.serialize(),
                async: false,
                cache: false,
                success: function(json) {
                    if (json.success){
                        alertify.success(json.msg);
                    }else{
                        alertify.error(json.msg);
                    }

                },
                error: function(xhr, status, error) {
                    let json = eval("(" + xhr.responseText + ")");
                    alertify.error(json.error, 5);
                }
            });
        }
    });

    window.MakeReservation = new MakeReservation();
})(window, jQuery, alertify);
