(function() {
    var citiesUrlBase = window.citiesUrlBase; // we'll define this from blade
    var $country = $('#country');
    var $city = $('#city');

    function loadCities(countryId, selectedCityId) {
        if (!countryId) {
            $city.html('<option value="">Select City</option>');
            return;
        }

        $city.html('<option value="">Loading...</option>');

        $.get(citiesUrlBase.replace('___ID___', countryId))
            .done(function (cities) {
                var opts = '<option value="">Select City</option>';
                $.each(cities, function(_, c) {
                    var sel = (selectedCityId && String(selectedCityId) === String(c.id)) ? ' selected' : '';
                    opts += '<option value="'+c.id+'"'+sel+'>'+c.name+'</option>';
                });
                $city.html(opts);
            })
            .fail(function () {
                $city.html('<option value="">Error loading cities</option>');
            });
    }

    // On change
    $('#country').on('change', function() {
        loadCities($(this).val(), null);
    });

    // On page load
    $(document).ready(function() {
        var initialCountryId = window.initialCountryId;
        var initialCityId    = window.initialCityId;
        if (initialCountryId) {
            loadCities(initialCountryId, initialCityId);
        }
    });
})();
