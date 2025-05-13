let iti;
let codigoPaisInput;
let nuevoCode;

const input = document.querySelector("#phone");

document.addEventListener('livewire:init', function () {

    let rutaComponent = (nombreComponent == 'create') ? 'event-component' : 'update-event';
    iti = window.intlTelInput(input, {
        separateDialCode: true,
        initialCountry: "auto",
        geoIpLookup: function (callback) {
            callback('co');
        },
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.11/build/js/utils.js",
    });
    
    input.addEventListener('change', function (e) {
        nuevoCode = iti.getSelectedCountryData();
        Livewire.dispatchTo('admin.' + rutaComponent, 'intlnumber', {
            valor: {
                phone: e.target.value,
                code: nuevoCode.dialCode,
                flag: nuevoCode.iso2
            },
        });
    });
    
    input.addEventListener("countrychange", function () {
        nuevoCode = iti.getSelectedCountryData();
        Livewire.dispatchTo('admin.' + rutaComponent, 'intlcode', {
            valor: {
                code: nuevoCode.dialCode,
                flag: nuevoCode.iso2
            },
        });
    });

    Livewire.on('editEvent', (event) => {
        if (event.phone != '') {
            iti.setNumber('+' + event.codigo + event.phone);
        } else {
            iti.setNumber('');
        }
    });

    Livewire.on('limpiar-phone', (event) => {
        input.value = '';
    });
});