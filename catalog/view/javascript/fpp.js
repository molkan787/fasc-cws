let cities;
let lang;
let fpp_cc;
let fpp_cr;
let citySelectNextJob = 'set';
function fpp_loadCities(){
    $.getJSON("/index.php?api_token=key&route=api/setting/list_stores&side=client", function( resp ) {
        if(resp.status == 'OK'){
            cities = resp.data.cities;
            lang = resp.lang;
            setOptions(getElt('pp_cities'), cities, '---', 'name_' + lang, 'city_id');
            switchElements('pp_loading', 'pp_city');
        }
    });
}

$(window).ready( () => {
    $('#pp_cities').change(pp_city_changed);
    $('#pp_regions').change(pp_region_changed);
    $('#pp_city_btn').click(pp_city_btn_click);
    $('#pp_reg_btn').click(register_btn_click);
    $('#pp_verify_btn').click(verify_btn_click);
    $('#pp_login_btn').click(login_btn_click);
});

function pp_city_changed(){
    let city_id = fpp_cc = this.value;
    if(!city_id){
        hideElts('#pp_regions,#pp_regions_header,#pp_city_btn');
        return;
    }
    for(var i = 0; i < cities.length; i++){
        if(cities[i].city_id == city_id){
            load_regions(cities[i].childs);
            break;
        }
    }
}
function load_regions(regions){
    if(regions.length > 0){
        setOptions(getElt('pp_regions'), regions, '---', 'name_' + lang, 'city_id');
        revealElts('#pp_regions,#pp_regions_header');
        hideElts('#pp_city_btn');
    }else{
        revealElts('#pp_city_btn');
        hideElts('#pp_regions,#pp_regions_header');
    }
}
function pp_region_changed(){
    let region = fpp_cr = this.value;
    if(region){
        revealElts('#pp_city_btn');
    }else{
        hideElts('#pp_city_btn');
    }
}

function pp_city_btn_click(){
   if(citySelectNextJob == 'set'){
        switchElements('pp_city', 'pp_loading');
        $.getJSON("/index.php?api_token=key&route=api/asd/set_si&city_id=" + fpp_cc + "&region_id="+fpp_cr, function( resp ) {
            if(resp.status == 'OK'){
                reloadWindow();
            }
        });
   }
}

// ==================== Registration =====================
let reg_firstname;
let reg_lastname;
let reg_phone;
let reg_email;
let reg_token;
let reg_cust_id;

function register_btn_click(){
    reg_firstname = $('#pp_reg_firstname').val();
    reg_lastname = $('#pp_reg_lastname').val();
    reg_phone = $('#pp_reg_phone').val();
    reg_email = $('#pp_reg_email').val();

    let error = false;
    if(reg_firstname.length < 3) { $('#pp_reg_firstname').css({borderColor: 'red'}); error = true; }
    if(reg_lastname.length < 3) { $('#pp_reg_lastname').css({borderColor: 'red'}); error = true; }
    if(reg_phone.length != 10) { $('#pp_reg_phone').css({borderColor: 'red'}); error = true; }

    if(error) return;

    let url = "/index.php?api_token=key&route=api/csc/register&firstname="+reg_firstname;
    url += "&lastname="+reg_lastname+"&telephone="+reg_phone+"&email="+reg_email;
    switchElements('pp_reg', 'pp_loading');
    $.getJSON(url, function( resp ) {
        if(resp.status == 'OK'){
            reg_token = resp.data.token;
            reg_cust_id = resp.data.customer_id;
            $('#pp_msg_phone').html(reg_phone);
            switchElements('pp_loading', 'pp_verify');
        }else if(resp.error_code == 'phone_exist'){
            alert('This phone number is already registrated!');
            switchElements('pp_loading', 'pp_reg');
            $('#pp_reg_phone').css({borderColor: 'red'});
        }
    });
}

function login_btn_click(){
    let phone = $('#pp_phone').val();
    if(phone.length != 10) { $('#pp_phone').css({borderColor: 'red'}); return; }

    let url = "/index.php?api_token=key&route=api/csc/login&telephone="+phone;
    switchElements('pp_login', 'pp_loading');
    $.getJSON(url, function( resp ) {
        if(resp.status == 'OK'){
            reg_token = resp.data.token;
            reg_phone = resp.data.telephone;
            $('#pp_msg_phone').html(reg_phone);
            switchElements('pp_loading', 'pp_verify');
        }else{
            alert('This phone number is not registrated!');
            $('#pp_phone').css({borderColor: 'red'});
            switchElements('pp_loading', 'pp_login');
        }
    });
}

function verify_btn_click(){
    let code = $('#pp_code').val();
    if(code.length != 6) { $('#pp_code').css({borderColor: 'red'}); return; }

    let url = "/index.php?api_token=key&route=api/csc/verify&code="+code;
    url += "&telephone="+reg_phone+"&token="+reg_token;

    switchElements('pp_verify', 'pp_loading');
    $.getJSON(url, function( resp ) {
        if(resp.status == 'OK'){
            reloadWindow();
        }else{
            alert('Verification code is incorrect!');
            $('#pp_code').css({borderColor: 'red'});
            switchElements('pp_loading', 'pp_verify');
        }
    });
}

// =============================================

function reloadWindow(){
    window.location.href = window.location.href.replace('#', '');
}

function alphaOnly(event) {
  var key = event.keyCode;
  return ((key >= 65 && key <= 90) || key == 8);
};

function getElt(elt_id){
    return document.getElementById(elt_id);
}
function crt_elt(tagName, parent){
    let elt = document.createElement(tagName);
    if(parent) parent.appendChild(elt);
    return elt;
}
function val(elt, value){
    elt.innerHTML = value;
}

function setOptions(parent, options, incAll, textProp, valueProp) {
    parent.innerHTML = '';
    var p_t = (typeof textProp == 'undefined') ? 'text' : textProp;
    var p_v = (typeof valueProp == 'undefined') ? 'id' : valueProp;
    
    if (options) {
        parent.removeAttribute('disabled');
    } else {
        parent.setAttribute('disabled', '');
        return;
    }

    if (typeof incAll != 'undefined') {
        var opt = crt_elt('option');
        opt.value = '';
        if (typeof incAll == 'boolean' && incAll)
        { val(opt, 'All'); parent.appendChild(opt); }
        else if (typeof incAll != 'boolean')
        { val(opt, incAll); parent.appendChild(opt); }
    }

    options = options.sort((a, b) => {
        if(a[p_t] < b[p_t]) { return -1; }
        if(a[p_t] > b[p_t]) { return 1; }
        return 0;
    });

    for (var i = 0; i < options.length; i++) {
        var opt = crt_elt('option', parent);
        val(opt, options[i][p_t]);
        opt.value = options[i][p_v];
    }
    parent.selectedIndex = 0;
}

function switchElements(fromElt, toElt) {
    anime({
        targets: "#" + fromElt,
        opacity: 0,
        easing: 'easeOutExpo',
        duration: 200,
        complete: function () {
            getElt(fromElt).style.display = "none";
            var elt = getElt(toElt);
            elt.style.display = (elt.tagName == 'IMG') ? 'inline-block' : 'block';
            anime({
                targets: "#" + toElt,
                opacity: 1,
                easing: 'easeOutExpo',
                duration: 200
            });
        }
    });
}

var fpp_celt;
function showPopup(elt, alch){
    fpp_alch = alch;
    $('#fpp > div.content').each(function (index){
        this.style.display = 'none';
        this.style.opacity = 0;
    });

    fpp_celt = document.getElementById('pp_' + elt);
    fpp_celt.style.display = 'block';
    fpp_celt.style.opacity = 1;
    if(!alch){
        document.getElementById('bbp').onclick = fpp_hide;
    }else{
        document.getElementById('bbp').onclick = null;
    }
    fpp_show();
}

function fpp_show(){
    document.getElementById('fpp').style.display = 'block';
    document.getElementById('bbp').style.display = 'block';
    anime({
        targets: "#fpp",
        opacity: 1,
        scale: 1,
        easing: 'easeOutQuad',
        duration: 300
    });
    anime({
        targets: "#bbp",
        opacity: 1,
        easing: 'easeOutExpo',
        duration: 600
    });
}
function fpp_hide(){
    anime({
        targets: "#fpp",
        opacity: 0,
        scale: 0.9,
        easing: 'easeOutExpo',
        duration: 600,
        complete: function (){
            document.getElementById('fpp').style.display = 'none';
        }
    });
    anime({
        targets: "#bbp",
        opacity: 0,
        easing: 'easeOutExpo',
        duration: 600,
        complete: function (){
            document.getElementById('bbp').style.display = 'none';
        }
    });
}

function revealElts(elts) {
    $(elts).each(function() {
        this.removeAttribute('disabled');
    });
    anime({
        targets: elts,
        opacity: 1,
        easing: 'easeOutExpo',
        duration: 300
    });
}
function hideElts(elts) {
    $(elts).each(function() {
        this.setAttribute('disabled', '');
    });
    anime({
        targets: elts,
        opacity: 0,
        easing: 'easeOutExpo',
        duration: 300
    });
}