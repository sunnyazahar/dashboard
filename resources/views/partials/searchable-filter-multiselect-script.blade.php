<script>
    window.initializeSearchableFilterMultiselect = function(selector, options) {
        var settings = $.extend({
            enableCaseInsensitiveFiltering: true,
            includeResetOption: true,
            resetText: 'Clear',
            filterPlaceholder: 'Type here',
            maxHeight: 420,
            buttonWidth: '100%',
            nonSelectedText: 'Click here',
            numberDisplayed: 1,
            nSelectedText: 'selected',
            buttonText: function(selectedOptions) {
                if (selectedOptions.length === 0) {
                    return 'Click here';
                }

                var firstSelection = $(selectedOptions[0]).text();
                return selectedOptions.length === 1 ? firstSelection : firstSelection + ', ...';
            },
            buttonTitle: function(selectedOptions) {
                var labels = [];

                selectedOptions.each(function() {
                    labels.push($(this).text());
                });

                return labels.join(', ');
            }
        }, options || {});

        $(selector).each(function() {
            $(this).multiselect(settings);
            $(this).closest('.multiselect-native-select').addClass('searchable-filter-wrapper');
        });
    };

    window.clearSearchableFilterMultiselect = function(selector, triggerChange) {
        $(selector).each(function() {
            $(this).multiselect('deselectAll', false);
            $(this).multiselect('updateButtonText');

            if (triggerChange !== false) {
                $(this).trigger('change');
            }
        });
    };
</script>
