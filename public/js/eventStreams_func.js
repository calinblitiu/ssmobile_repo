$(function ($) {
    if (typeof currentZoneOffset !== 'undefined') {
        momentZone = moment.tz.guess();
        currentZoneOffset = moment.tz(momentZone).utcOffset() / 60;
        tz = currentZoneOffset;
    } else {
        var tzOptions = document.getElementById('offset');
        var tz = tzOptions.options[tzOptions.selectedIndex].value;
    }

    $('#event-table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        paging: false,
        searching: false,
        info: false,
        ordering: false,
        ajax: 'getAllEvents/24',
        columns: [
            {
                data: 'start_date',
                render: function (data, type, full) {
                    if (full.game_minute != '') {
                        return '<span class="game_minute">' + full.game_minute + '</span>';
                    } else {
                        var utcStart = moment.utc(data).utcOffset('UTC');
                        var startDate = utcStart.utcOffset(tz * 60).format('D MMM HH:mm');
                        return '<span class="event-time" data-eventtime="' + data + '">' + startDate + '</span>';
                    }
                }
            },
            {
                data: 'competition_name',
                render: function (data, type, full) {
                    return '<img src="images/competitions/small/' + full.competition_logo + '" alt="" width="30 "height="30">';
                }
            },
            {
                data: 'home_team',
                render: function (data, type, full) {
                    return data + '&nbsp;<img src="images/teams/small/' + full.home_team_logo + '" alt="" width="30" "height="30">';
                },
                class: 'text-right'
            },
            {
                data: 'event_status',
                render: function (data, type, full) {
                    if (data == '') {
                        return 'vs';
                    } else {
                        return data;
                    }
                },
                class: 'text-center'
            },
            {
                data: 'away_team',
                render: function (data, type, full) {
                    return ' <img src="images/teams/small/' + full.away_team_logo + '" alt="" width="30" "height="30">&nbsp;' + data;
                },
                class: 'text-left'
            },
            {
                data: 'event_id',
                render: function (data, type, full) {
                    return '<a href="{{ secure_url('streams') }}/' + data + '/' + full.home_team_slug + '_vs_' + full.away_team_slug + '" class="btn btn-rss">Watch</a>';
                }
            }
        ],
        initComplete: function (settings, json) {
            $('#event-table thead').hide();
        },
    });

    $('#event-table').on('click', 'tbody tr', function () {
        window.location.href = $(this).find('.btn-rss').attr('href');
    });
});