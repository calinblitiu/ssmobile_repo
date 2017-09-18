var el_tooltip = $('.highlight[data-toggle="tooltip"]');

window.onload = function() {
    var slug = window.location.hash.substr(1);
    if( slug == "" ){
        setTimeout(function () {
            el_tooltip.tooltip({trigger: 'manual'}).tooltip('hide');
        }, 1000);

        return;
    }

    el = $('table [data-slug="'+slug+'"]');

    if (el.length < 1) return;
    if( el.parent().parent().hasClass('stream_block'))
        el = el.parent().parent();

    var elOffset = el.offset().top;
    var elHeight = el.height();
    var windowHeight = $(window).height();
    var offset;

    if (elHeight < windowHeight) {
        offset = elOffset - ((windowHeight / 2) - (elHeight / 2));
    }
    else {
        offset = elOffset;
    }

    target = $('table [data-slug="'+slug+'"]').parent();
    target.addClass('highlighed_cell');
    target.siblings().addClass('highlighed_cell');
    $('.highlighed_cell [data-toggle="tooltip"]').parent().css('z-index', 1700);
    $('.highlighed_cell .verified').parent().css('z-index', 1800);

    if( el.hasClass('stream_block'))
    {
        element = $('.highlighed_cell').parent();
        $('.stream_block').removeClass('active');
        element.addClass('active');

        var siblings = element.siblings().length;
        width = 100 - 6 * ( siblings );
        element.css('width', 'calc('+ width+'%' + ' - ' + (siblings+1) + 'px)');

        $('.stream_block').not('.active').css('width', '6%');

    }

    el_tooltip.tooltip('hide');

    $('.blurry').show();
    $('html, body').animate({
        scrollTop: offset
    }, 1000, function(){
        el_tooltip.tooltip({trigger: 'manual'}).tooltip('hide');
    });
};

window.onresize = function() {
    el_tooltip.tooltip('hide');
}

$('.collapse').on('shown.bs.collapse', function(){
    $(this).parent().find(".fa-expand").removeClass("fa-expand").addClass("fa-compress");
}).on('hidden.bs.collapse', function(){
    $(this).parent().find(".fa-compress").removeClass("fa-compress").addClass("fa-expand");
});

$(".blurry").on('click', function(){
    $(this).hide();
    // $('.highlighed_cell').removeClass('highlighed_cell');
});

function edit_form( streamId )
{
    if( !streamId )
        return;
    $.post(
        "{{ secure_url('updateStream') }}",
{"_token": "{{ csrf_token() }}", "stream_id": streamId },
    function (data, status) {
        streamInfo = JSON.parse( data );

        $('#stream_id').val( streamId );
        $('#type_selector').val( streamInfo.stream_type );
        $('#url').val( streamInfo.url );
        $('#language_selector').val( streamInfo.language );
        $('#stream_quality').val( streamInfo.quality );
        if( streamInfo.compatibility == "Yes")
            $('#mobile_compatible').attr('checked', true);

        $('#adNumber').val( streamInfo.ad_number );
        if( streamInfo.nsfw == 1 )
            $('#nsfw').attr('checked', true);
        $('#otherInfo').val( streamInfo.other_info );
        $('.selectpicker').selectpicker('refresh');
    });
}

$(function ($) {
    $('#streamForm').parsley();
    $('.selectpicker').selectpicker({
        style: 'btn-default',
        size: 6
    });
});

function selectType(el) {
    var streamType = el.value;
    if (streamType == 'http') {
        $('#url').prop('disabled', false);
        $('#url').attr("data-parsley-pattern", "(?=(http:\/\/|https:\/\/)).*");
        $('#url').attr('data-parsley-error-message', 'Please only use http://, https://');
    }
    else if (streamType == 'Acestream') {
        $('#url').prop('disabled', false);
        $('#url').attr("data-parsley-pattern", "(?=(acestream:\/\/)).*");
        $('#url').attr('data-parsley-error-message', 'Please only use acestream://');
    }
    else if (streamType == 'sopcast') {
        $('#url').prop('disabled', false);
        $('#url').attr("data-parsley-pattern", "(?=(sop:\/\/)).*");
        $('#url').attr('data-parsley-error-message', 'Please only use sop://');
    }
    else if (streamType == 'VLC') {
        $('#url').prop('disabled', false);
        $('#url').attr("data-parsley-pattern", "(?=(http:\/\/|https:\/\/)).*");
        $('#url').attr('data-parsley-error-message', 'Please only use http://, https://');
    }
    else if (streamType == 'Other') {
        $('#url').disabled = false;
        $('#url').removeAttr("data-parsley-pattern");
        $('#url').removeAttr('data-parsley-error-message');
    }
}

$('#submitForm').click(function(e) {
    selectType( document.getElementById('type_selector') );
    e.preventDefault();

    if( $('form#streamForm').parsley().validate() )
    {
        $.post(
            "{{ secure_url('updateStream') }}",
            $('form#streamForm').serialize(),
            function ( res, status1) {
                if( res == 1 )
                    swal({title: "Successfully updated!", type: "success"});
                else
                    swal({title: res, type: "error"})
            });
    }
});

//for Whatch button toolTip
$(".geoLock, .isExpandable").click(function () {
    var dataId = $(this).attr('data-stream-id');
    if( tr_el.hasClass('open') ) {
        $(".tooltip_" + dataId).show();
        //$('.btnWatch_'+ dataId).removeClass('not-active');
    }else{
        $(".tooltip_" + dataId).hide();
        //$('.btnWatch_'+ dataId).addClass('not-active');
    }
});
//End

$(".clickable").click(function () {
    tr_el = $(this).parent();
    if( tr_el.hasClass('geoLock') || tr_el.hasClass('isExpandable') )
        return;
    else
        window.open(tr_el.data("href"));
});

$(".geoLock>td").click(function () {

    tr_el = $(this).parent();
    if( tr_el.hasClass('geoLock') )
    {
        if( tr_el.hasClass('open') )
        {
            tr_el.removeClass('open');
            $('.append_tr td > div').slideUp( 300, function(){ $('.append_tr').remove();} );
        }
        else{
            $('.append_tr').remove();
            $('.append_tr td > div').slideUp( 300, function(){ $('.append_tr').remove();} );

            tr_el.addClass('open');
            countTD = $(this).siblings().length + 1;

            //Sponsor banner
            var sponsorBanner = '';
            if(tr_el.hasClass('sponsor')){
                /*sponsorBanner = '<div class="row">'+
                 '<div class="col-xs-12" style="margin-bottom:10px;">' +
                 '<img src="/images/banners/v01.jpg" class="img-responsive"/></div>' +
                 '<div class="col-xs-12" style="margin-bottom:10px">' +
                 '<img src="/images/banners/v02.jpg" class="img-responsive"/></div>'+
                 '</div>';*/
                sponsorBanner = '<div class="row">'+
                    '<div class="col-xs-12" style="margin-bottom:10px;">' +
                    '<a href="https://www.fubo.tv/watch/SoccerStreams?irad=366554&irmp=376982" target="_blank"><img src="/images/banners/v01.jpg" class="img-responsive"/></a></div>' +
                    '</div>';
            }

            html = "";


            // html = sponsorBanner +'<p>This stream is geo-locked. Please consider using one of the following VPN services to support us.</p>'+
            //         '<div class="row"><div class="col-xs-3"></div>'+
            //           '<div class="col-xs-2 vpnLogo" style="border: 1px solid #6cd22d;">' +
            //         '<span class="vpnlogo-content"><div class="vpnlogo-text">For Best Speed</div><a href="https://billing.purevpn.com/aff.php?aff=30171" target="_new">'+
            //             '<img src="/images/vpn/purevpn.png" class="img-responsive"/></a></span></div>'+
            //         '<div class="col-xs-2 vpnLogo" style="border: 1px solid #d60017;">' +
            //         '<span class="vpnlogo-content"><div class="vpnlogo-text">For Best Speed</div><a href="https://www.linkev.com/?a_aid=streams17" target="_new">'+
            //         '<img src="/images/vpn/vpn-express.png" class="img-responsive"/>'+
            //         '</a></span></div>'+
            //           '<div class="col-xs-2 vpnLogo" style="border: 1px solid #0063a9;">' +
            //         '<span class="vpnlogo-content"><div class="vpnlogo-text">For Best security</div><a href="https://go.nordvpn.net/aff_c?offer_id=15&aff_id=4565" target="_new">'+
            //             '<img src="/images/vpn/vpn-nord.png" class="img-responsive"/></a></span></div>'+
            //         '</div>';


            if( tr_el.next().hasClass('tooltip'))
                tr_el.next().after('<tr class="append_tr"><td style="background:#faf3b3;padding:0;position:relative;z-index:1500;text-align: center;" colspan='+countTD+'>'+html+'</td></tr>');
            else
                tr_el.after('<tr class="append_tr"><td style="background:#faf3b3;padding:0;position:relative;z-index:1500;text-align: center;" colspan='+countTD+'>'+html+'</td></tr>');

            $('.append_tr').children('td').wrapInner('<div>');
            $('.append_tr td > div').css( { "display":"none", "padding": "20px" });
            $('.append_tr td > div').slideDown( 300 );
        }
    }
    else
        window.open(tr_el.data("href"));
});

//$(".isExpandable > td, .clickable").click(function () {
$(".isExpandable>td").click(function () {
    tr_el = $(this).parent();
    if( tr_el.hasClass('isExpandable') )
    {
        if( tr_el.hasClass('open') )
        {
            tr_el.removeClass('open');
            $('.append_tr td > div').slideUp( 300, function(){ $('.append_tr').remove();} );
        }
        else{
            $('.append_tr').remove();
            $('.append_tr td > div').slideUp( 300, function(){ $('.append_tr').remove();} );

            tr_el.addClass('open');
            countTD = $(this).siblings().length + 1;
            /*html = '<div class="row">'+
             '<div class="col-xs-12" style="margin-bottom:10px;">' +
             '<img src="/images/banners/v01.jpg" class="img-responsive"/></div>' +
             '<div class="col-xs-12">' +
             '<img src="/images/banners/v02.jpg" class="img-responsive"/></div>'+
             '</div>';*/
            html = '<div class="row">'+
                '<div class="col-xs-12" style="margin-bottom:10px;">' +
                '<a href="https://www.fubo.tv/watch/SoccerStreams?irad=366554&irmp=376982" target="_blank"><img src="/images/banners/v01.jpg" class="img-responsive"/></a></div>' +
                '</div>';


            if( tr_el.next().hasClass('tooltip'))
                tr_el.next().after('<tr class="append_tr"><td style="background:#faf3b3;padding:0;position:relative;z-index:1500;text-align: center;" colspan='+countTD+'>'+html+'</td></tr>');
            else
                tr_el.after('<tr class="append_tr"><td style="background:#faf3b3;padding:0;position:relative;z-index:1500;text-align: center;" colspan='+countTD+'>'+html+'</td></tr>');

            $('.append_tr').children('td').wrapInner('<div>');
            $('.append_tr td > div').css( { "display":"none", "padding": "20px" });
            $('.append_tr td > div').slideDown( 300 );
        }
    }
    else
        window.open(tr_el.data("href"));
});


/*Real time score update*/

function updateScore(updateCheck){
    var id = $("#event_minute").data("id");
    var data = [id];

    $.ajax({
        url: document.location.origin + '/updatehomepagescores',
        dataType: 'json',
        data: {
            data: data
        },
        success: function (result) {
            if( !(result['result'] == 0) )
                for (var i = result['result'].length - 1; i >= 0; i--) {
                    if(result['result'][i]["event_minute"] != null)
                        $("#event_minute").text("'"+result['result'][i]["event_minute"]);
                    if(result['result'][i]["event_status"] != null)
                        $("#event_score").text(result['result'][i]["event_status"]);
                }
        },
        error: function(result) {
        }
    });
}

if($('#event_minute').data("running") && $('#updateScore').attr("value") == "checked"){
    setInterval(function(){
        updateScore();
    }, 60000)
}
/*End of real time score update*/

/* Multiple Stream Features */
$('.stream_heading').hover( function(){
    el = $(this).parent();
    el.siblings().removeClass('active');
    el.addClass('active');

    var siblings = el.siblings(".stream_block:visible").length;
    width = 100 - 6 * ( siblings );

    el.css('width', 'calc('+ width+'%' + ' - ' + (siblings+1) + 'px)');
    $('.stream_block').not('.active').css('width', '6%');

});

$('.multiple_streams').each( function(){
    $(this).children().first().addClass('active');
});

$('.stream_block.active').each(function(){
    var siblings = $(this).siblings(".stream_block:visible").length;
    width = 100 - 6 * ( siblings );
    $(this).css('width', 'calc('+ width+'%' + ' - ' + (siblings+1) + 'px)');
    $('.stream_block').not('.active').css('width', '6%');

});

$('#streams').on('change', function (e) {
    $('.stream_block.active').each(function(){
        var siblings = $(this).siblings(".stream_block:visible").length;
        width = 100 - 6 * ( siblings );
        $(this).css('width', 'calc('+ width+'%' + ' - ' + (siblings+1) + 'px)');
        $('.stream_block').not('.active').css('width', '6%');
    });

    $('#streams-table tr:not(.clickable-row)').each(function(){
        if($(this).children(':visible').length == 0) {
            $(this).hide();
        }
        else
            $(this).show();
    });
});

$('#streams-table tr').each(function(){
    if($(this).children().length == 1 && $(this).children('td').children().length == 0 ){
        $(this).hide();
    }
});

$('#streams-table tr .dropdown').hover(
    function(){
        $(this).closest('tr.stream_block').children().css('padding-bottom', '120px');
        $(this).closest('div.stream_block').children().css('margin-bottom', '120px');
        $(this).parent().css('overflow', 'visible');
    },

    function () {
        $(this).closest('tr.stream_block').children().css('padding-bottom', '8px');
        $(this).closest('div.stream_block').children().css('margin-bottom', '0');
        $(this).parent().css('overflow', 'hidden');
    }
);

$('#streams-table tr .dropdown-content').click( function(){
    $(this).closest('tr.stream_block').children().css('padding-bottom', '8px');
    $(this).closest('div.stream_block').children().css('margin-bottom', '0');
    $(this).parent().parent().css('overflow', 'hidden');
});