<aside class="main-sidebar">
  <!-- sidebar: style can be found in sidebar.less -->
  <section class="sidebar">
    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu">
      <li class="header">MAIN NAVIGATION</li>
      <li class="treeview @if(Route::current()->uri=='moderator/stream/') active @endif">
        <a href="#">
          <i class="fa fa-bullhorn"></i> <span>Streams</span>
          <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="{{ secure_url('moderator/stream') }}"><i class="fa fa-circle-o"></i>Published Streams</a></li>
          <li><a href="{{ secure_url('moderator/stream/waitingApprove') }}"><i class="fa fa-circle-o"></i>Waiting Approve</a></li>
          <li><a href="{{ secure_url('moderator/stream/addStream') }}"><i class="fa fa-circle-o"></i>Add stream</a></li>
        </ul>
      </li>
      <li class="treeview @if(Route::current()->uri=='moderator/event/') active @endif">
        <a href="#">
          <i class="fa fa-bullhorn"></i> <span>Events</span>
          <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="{{ secure_url('moderator/event/createMatch') }}"><i class="fa fa-circle-o"></i>Create Match Event</a></li>
          <li><a href="{{ secure_url('moderator/event/createEvent') }}"><i class="fa fa-circle-o"></i>Create Event</a></li>
          <li><a href="{{ secure_url('moderator/event') }}"><i class="fa fa-circle-o"></i>Events list</a></li>
        </ul>
      </li>
      <!-- channels -->
      <li class="treeview @if(Route::current()->uri=='moderator/channel/') active @endif">
        <a href="#">
          <i class="fa fa-bullhorn"></i> <span>Channels</span>
          <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="{{ secure_url('moderator/channel/createChannel') }}"><i class="fa fa-circle-o"></i>Create Channel</a></li>
          <li><a href="{{ secure_url('moderator/channel') }}"><i class="fa fa-circle-o"></i>Channels list</a></li>
        </ul>
      </li>
      
      <li class="treeview @if(Route::current()->uri=='moderator/user') active @endif">
        <a href="#">
          <i class="fa fa-users"></i> <span>Users</span>
          <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="{{ secure_url('moderator/user/addUser') }}"><i class="fa fa-circle-o"></i>Add user</a></li>
          <li><a href="{{ secure_url('moderator/user') }}"><i class="fa fa-circle-o"></i>List of users</a></li>
          <li><a href="{{ secure_url('moderator/user/broadcast') }}"><i class="fa fa-circle-o"></i>Broadcast notification</a></li>
          <li><a href="{{ secure_url('moderator/user/messages') }}"><i class="fa fa-circle-o"></i>User Messages</a></li>
        </ul>
      </li>
      <li class="treeview  @if(Route::current()->uri=='moderator/page') active @endif">
        <a href="#">
          <i class="fa fa-users"></i> <span>Pages</span>
          <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
        </a>
        <ul class="treeview-menu">
          <li><a href="{{ secure_url('moderator/page/1') }}"><i class="fa fa-circle-o"></i>Rules</a></li>
          <li><a href="{{ secure_url('moderator/page/2') }}"><i class="fa fa-circle-o"></i>Contact us</a></li>
          <li><a href="{{ secure_url('moderator/page/3') }}"><i class="fa fa-circle-o"></i>DMCA</a></li>
          <li><a href="{{ secure_url('moderator/page/4') }}"><i class="fa fa-circle-o"></i>Faq</a></li>
        </ul>
      </li>
      <li class="header">Other</li>
      @if(\Illuminate\Support\Facades\Auth::user()->role==2)
        <li><a href="{{ secure_url('moderator/log') }}"><i class="fa fa-circle-o text-red"></i> <span>Activity log</span></a></li>
      @endif
      <li><a target="_blank" href="{{ secure_url('/') }}"><i class="fa fa-circle-o text-blue"></i> <span>Main website</span></a></li>
    </ul>
  </section>
  <!-- /.sidebar -->
</aside>