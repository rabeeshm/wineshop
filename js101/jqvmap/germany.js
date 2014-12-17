jQuery(document).ready(function() {
    jQuery('#vmap').vectorMap({
        map: 'germany_en',
        onRegionClick: function(element, code, region)
        {
            redirectToList(code);
        }
    });
});
function markSelected(code) {
    if (code.toLowerCase() == 'de')
        return;
    jQuery(document).ready(function() {
        jQuery('#vmap').vectorMap('select', code);
    });
}