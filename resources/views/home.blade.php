@extends('master')

@section('content')
  <div id="manage_events">
    <table class="table table-striped table-hover" id="event-table">
      <thead>
      <tr>
        <th>Date</th>
        <th>Competition</th>
        <th>Home Team</th>
        <th>Status</th>
        <th>Away Team</th>
        <th>Action</th>
      </tr>
      </thead>
      <tbody>
      
      </tbody>
    </table>
  </div>
@endsection
@section('scripts')
  <script>
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
              ajax: 'getAllEvents/50/+3:30',
              columns: [
                  {
                      data: 'start_date',
                      render: function (data) {
                          var utcStart = moment.utc(data).utcOffset('UTC');
                          var startDate = utcStart.utcOffset(tz * 60).format('D MMM HH:mm');
                          return '<span class="event-time" data-eventtime="' + data + '">' + startDate + '</span>';
                      }
                  },
                  {
                      data: 'competition_name',
                      render: function (data, type, full) {
                          return '<img src="images/competitions/small/' + full.competition_logo + '" alt="" width="30">';
                      }
                  },
                  {
                      data: 'home_team',
                      render: function (data, type, full) {
                          return '<img src="images/teams/small/' + full.home_team_logo + '" alt="" width="30"> ' + data;
                      }
                  },
                  {
                      data: 'event_status'
                  },
                  {
                      data: 'away_team',
                      render: function (data, type, full) {
                          return data + ' <img src="images/teams/small/' + full.away_team_logo + '" alt="" width="30">';
                      },
                      class: 'text-right'
                  },
                  {
                      data: 'event_id'
                  }
              ]
          });
        
        /* $('#event-table').on('click', 'tbody tr', function () {
         window.location.href = $(this).attr('href');
         });*/
      });
  </script>
@endsection