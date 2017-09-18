
$(function ($) {
    if (typeof currentZoneOffset !== 'undefined') {
        momentZone = moment.tz.guess();
        currentZoneOffset = moment.tz(momentZone).utcOffset() / 60;
        tz = currentZoneOffset;
    } else {
        var tzOptions = document.getElementById('offset');
        var tz = tzOptions.options[tzOptions.selectedIndex].value;
    }

    $('#competition_selector').on('change', function (e) {

        $("tbody tr").hide();
        if(this.value == "All")
        {
            $("tbody tr").show();
        }
        else
        {
            $("tbody tr[competition='"+this.value+"']").show();
        }

        // table.columns(0).search(this.value).draw();
    });


    // $('#eventsTable').DataTable({
    //   "responsive": true,
    //   "scrollCollapse": true,
    //   "paging": false

    // });

    $('#eventsTable tbody tr').on('click', function () {
        if ($("#eventsTable").find('td').hasClass('matches_divider')) {
            return;
        }
        window.location.href = $("#eventsTable").find('.btn-rss').attr('href');
    });


    // table.search( $('#competition_selector').val() ).draw();
});

function notificationAction(el) {
    $.post(
        "{{ secure_url('profile/notificationAction') }}",
    {"_token": "{{ csrf_token() }}", "id": el, "action": 1},
        function (data, status) {
            console.log(data)
        });
}

function verifyAlert()
{
    $.post(
        "{{ secure_url('profile/closeAlert') }}",
{"_token": "{{ csrf_token() }}"},
    function (data, status) {
        console.log(data)
    });
}

function updateScore(updateCheck){
    if(document.getElementById('updateScore').checked){
        var data = [];
        $('.running').each(function(i, obj){
            data.push($(obj).data('id'));
        })

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
                            $("#running_"+result['result'][i]["event_id"]+"_minute").text("'"+result['result'][i]["event_minute"]);
                        if(result['result'][i]["event_status"] != null)
                            $("#running_"+result['result'][i]["event_id"]+"_score").text(result['result'][i]["event_status"]);
                    }
            },
            error: function(result) {
            }
        });
    }
}

updateScore();

setInterval(function(){
    updateScore();
}, 60000)

$("#updateScore").change(function(){
    var check = document.getElementById('updateScore').checked;
    var url = document.location.origin + '/updatehomepagescores';
    window.location = url + "?check=" + check;
})

$("a[id^=show_]").click(function(event) {
    $opened = $(".open");
    $opened.slideToggle("slow");
    $opened.removeClass("open");
    if ($opened.attr('id') != "extra_" + $(this).attr('id').substr(5)){
        $d = $("#"+"extra_" + $(this).attr('id').substr(5));
        $("#extra_" + $(this).attr('id').substr(5)).slideToggle("slow");
        $d.addClass("open");
    }
    event.preventDefault();
})

/*Instant match search*/
var $rows = $('#eventsTable tr');
$('#instant_search').keyup(function() {
    var val = '^(?=.*\\b' + $.trim($(this).val()).split(/\s+/).join('\\b)(?=.*\\b') + ').*$',
        reg = RegExp(val, 'i'),
        text;

    $rows.show().filter(function() {
        text = $(this).text().replace(/\s+/g, ' ');
        return !reg.test(text);
    }).hide();
});
