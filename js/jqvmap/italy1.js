jQuery(document).ready(function() {
    jQuery('#vmap').vectorMap({
        map: 'italy_en',
        onRegionClick: function(element, code, region)
        {
            redirectToList(code);
        }
    });
});
function markSelected(code) {
    if (!code || code == 'it' || code == 'product')
        return;
    jQuery(document).ready(function() {
        jQuery('#vmap').vectorMap('select', code);
    });
}

