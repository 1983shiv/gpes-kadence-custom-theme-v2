jQuery(document).ready(function($){
    // Parse the URL
    function getParameterByName(name) {
        name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
        var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
            results = regex.exec(location.search);
        return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
    }

    // Give the URL parameters variable names
    var utm_source      = getParameterByName('utm_source');
    var utm_medium      = getParameterByName('utm_medium');
    var utm_campaign    = getParameterByName('utm_campaign');
    var utm_term        = getParameterByName('utm_term');
    var utm_content     = getParameterByName('utm_content');
    var utm_channel_group = utm_source ? utm_source + "/" + utm_medium : ""
    ;
    // Set the cookies
    if (utm_source) setCookie('gp_utm_source', utm_source);
    if (utm_medium) setCookie('gp_utm_medium', utm_medium);
    if (utm_campaign) setCookie('gp_utm_campaign', utm_campaign);
    if (utm_term) setCookie('gp_utm_term', utm_term);
    if (utm_content) setCookie('gp_utm_content', utm_content);
    if (utm_channel_group) setCookie('gp_channel_group', utm_channel_group);

    $("input#input_1_7").val(getCookie('gp_utm_source'));
    $("input#input_1_8").val(getCookie('gp_utm_medium'));
    $("input#input_1_9").val(getCookie('gp_utm_campaign'));

    $("input#input_2_2").val(getCookie('gp_utm_source'));
    $("input#input_2_3").val(getCookie('gp_utm_medium'));
    $("input#input_2_19").val(getCookie('gp_utm_campaign'));
    $("input#input_2_21").val(getCookie('gp_utm_term'));
    $("input#input_2_22").val(getCookie('gp_utm_content'));
    $("input#input_2_20").val(getCookie('gp_channel_group'));
    
});

function setCookie(cname, cvalue) {
    exdays = 60;
    const d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    let expires = "expires="+ d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}
  

function getCookie(cname) {
    let name = cname + "=";
    let decodedCookie = decodeURIComponent(document.cookie);
    let ca = decodedCookie.split(';');
    for(let i = 0; i <ca.length; i++) {
      let c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return "";
}