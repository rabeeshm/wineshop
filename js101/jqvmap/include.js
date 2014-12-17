/*!
 *@author : rabeesh@confianzit.biz
 */
function redirectToList(code) {
    jQuery.ajax({
        type: "POST",
        url: "http://10.254.101.75/wineshop/regions/region/getRegion",
        data: "code=" + code,
        success: function(data) {
                  var url;
                  var min = getParameterByName('min');
                  var max = getParameterByName('max');
                  if(!min && !max){
                    url =  data;
                  } else {
                     url =  data+"?min="+min+"&max="+max; 
                  }
            window.location.href = url
        }
    });
}
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}