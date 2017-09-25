
jQuery(document).ready(function($) {
    var $trackingFormSubmitSelector = '.js-track-shipment';

    $($trackingFormSubmitSelector).on('click', function(e) {
        e.preventDefault();
        var $button = $(this),
            $form = $button.closest('form');
        $trackingNumber = $('#trackingNumber').val(),
            $buttonText = $button.html();
        $button.html("<span class='fa fa-spin fa-spinner'></span>");
        $button.prop('disabled', true);
        $('#trackingResult').html('');

        $.ajax({
            method: 'post',
            url: PMPObject.ajaxUrl,
            dataType: 'json',
            data: {
                action: 'track_package',
                trackingNumber: $trackingNumber
            }
        }).then(
            function(result) {
                $('#trackingResult').html(result.html);
            },
            function(error) {
                $('#trackingResult').html(error.responseJSON.html);
            }
        ).always(
            function() {
                $button.blur();
                $button.html($buttonText);
                $button.prop('disabled', false);
            }
        );

    });
});