jQuery(document).ready(function($) {
    // var submitButtonOriginalText = 'SIGN UP!'; // default

    var birthdayInputId = $("label").filter(function() {
        return $(this).text().trim() === "Birthday";
    }).attr('for');

    var $birthdayField = $('#' + birthdayInputId);

    $birthdayField.on('blur', function() {
        var dob = $(this).val();
        var age = calculateAge(new Date(dob));
        var $dobField = $(this).closest('.gfield');

        $('.custom-tooltip').remove();

        if(age < 21) {

            var $tooltip = $('<div class="custom-tooltip" style="position: absolute;">Sorry, you must be at least 21 years old to sign up.</div>');

			$($dobField).append($tooltip);

			var fieldPos = $dobField.offset();
            $tooltip.css({
                top: fieldPos.top - $tooltip.outerHeight() + 155,
                left: fieldPos.left,
                background: '#f7d7da',
                color: '#721c24',
                padding: '8px',
                borderRadius: '4px',
                display: 'none',
                zIndex: 1000,
                fontSize: '0.875rem',
                textAlign: 'center'
            });

			$tooltip.fadeIn('fast');

            var $submitButton = $(this).closest('form').find('input[type="submit"], button[type="submit"]');

            submitButtonOriginalText = $submitButton.val();
            console.log("after fail:" + submitButtonOriginalText);

            // Change submit button text and disable it
            // $submitButton.val('Sorry, you must be at least 21 years old to sign up').prop('disabled', true).addClass('disabled');
            $submitButton.prop('disabled', true).addClass('disabled');

        } else {
            var $submitButton = $(this).closest('form').find('input[type="submit"], button[type="submit"]');
            // Restore original submit button text and enable it if previously disabled
            // $submitButton.val(submitButtonOriginalText).prop('disabled', false).removeClass('disabled');
            $submitButton.prop('disabled', false).removeClass('disabled');

        }
    });

    function calculateAge(birthday) {
        var ageDifMs = Date.now() - birthday.getTime();
        var ageDate = new Date(ageDifMs);
        return Math.abs(ageDate.getUTCFullYear() - 1970);
    }
});
