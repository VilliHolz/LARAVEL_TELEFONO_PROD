function initializeAutocomplete() {
    let ubicacion = document.getElementById('location');
    let reception_place = document.getElementById('reception_place');

    let autocompleteLocation = createAutocomplete(ubicacion, 'inputUpdated');
    let autocompleteReceptionPlace = createAutocomplete(reception_place, 'inputUpdatedReceptionPlace');
}

function createAutocomplete(inputElement, livewireMethod) {
    let autocomplete = new google.maps.places.Autocomplete(inputElement);

    autocomplete.addListener('place_changed', function () {
        let place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }

        // Asignar el nombre del lugar al campo de entrada
        inputElement.value = place.name;

        // Llamar al método correspondiente de Livewire
        Livewire.find(inputElement.closest('[wire\\:id]')
            .getAttribute('wire:id')).call(livewireMethod, place.name);
    });

    // Asegurarse de que el z-index del contenedor sea adecuado
    setTimeout(function () {
        let autocompleteContainer = document.querySelectorAll('.pac-container');
        autocompleteContainer.forEach(container => {
            container.style.zIndex = '1056';
        });
    }, 500);
    

    return autocomplete;
}

// Llama esta función una vez que la API de Google Maps esté cargada
initializeAutocomplete();