let iti;


export function initializePhone(inputEl, outputEl, messageEl = null) {
    let input = document.querySelector(inputEl)
    let output = document.querySelector(outputEl);
    iti = window.intlTelInput(input, {
        nationalMode: true,
        utilsScript: "/front/js/intl-tel-input/utils.js",
        initialCountry: "ke"
    });
    iti.promise.then(function () {
        handleChange();
    });

    let handleChange = function () {
        input.value = input.value.replaceAll("+", "");
        input.value = input.value.replaceAll("-", "");

        if(input.value.substring(0, iti.selectedCountryData.dialCode.length) ===  iti.selectedCountryData.dialCode) {
            input.value = input.value.substring(iti.selectedCountryData.dialCode.length);
        }

        if (input.value[0] === '0')
        {
            input.value = input.value.slice(1).replace(' ', '')
        }
        if (messageEl && input.value.length > 7)
        {
            $('span:contains("Please enter a valid phone number")').html("");
            $(messageEl).html("Please enter a valid phone number")
        }
        if (iti.isValidNumber())
        {
            $(messageEl).html("")
            $('span:contains("Please enter a valid phone number")').html("");
        }
        output.value = "+" + iti.selectedCountryData.dialCode + '-' + iti.telInput.value;
        console.log(output.value);

    };

    input.addEventListener('change', handleChange);
    input.addEventListener('keyup', handleChange);
    return iti
}
