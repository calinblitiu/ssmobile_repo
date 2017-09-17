<div class="col-md-3">
  <div class="row">
    <div class="col-md-12">
      <div class="panel panel-rss">
        <div class="panel-heading">User Menu</div>
        <div class="panel-body">
          <ul>
            <li><a href="{{ secure_url('profile') }}">Profile</a></li>
            <li><a href="{{ secure_url('profile/messages') }}"> Messages
                <span class="profile-page-notification-count" style="display:none;position:absolute;top: 76px;left: 137px;">( {{ count($unread) }} )</span> 
            </a></li>
            <li><a href="{{ secure_url('profile/favourite') }}"> Your Favourites </a></li>
            <li><a href="{{ secure_url('submit') }}">Submit Stream</a></li>
            @if(\Illuminate\Support\Facades\Auth::user()->role>=1)
              <li><a href="{{ secure_url('moderator/dashboard') }}">Moderator</a></li>
            @endif
            <li>
              <a href="{{ secure_url('logout') }}" onclick="event.preventDefault();
         document.getElementById('logout-form').submit();">Logout</a>
            </li>
          </ul>

          <form id="logout-form" action="{{ secure_url('logout') }}" method="POST" style="display: none;">
            {{ csrf_field() }}
          </form>

        </div>
      </div>
    </div>
  </div>
</div>
