jQuery(document).ready(function() {
    jQuery('#vmap').vectorMap({
        map: 'france_fr',
        onRegionClick: function(element, code, region)
        {
            redirectToList(code);
        }
    });
});
function markSelected(code) {
    if(!code || code.toLowerCase() == 'fr' || code == 'product')
        return;
    jQuery(document).ready(function() {
        jQuery('#vmap').vectorMap('select', code);
    });
}
