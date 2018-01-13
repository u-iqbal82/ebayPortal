<nav class="navbar navbar-default top-menu">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
      <a class="navbar-brand" href="{{ url('/') }}">
        <img src="https://www.ithinkmedia.co.uk/wp-content/uploads/2017/09/itm-logo-trans.png" width="140" alt="iThinkMedia" />
      </a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav navbar-right">
        @if(Auth::guest())
            <li><a href="{{ url('/login') }}">Login</a></li>
        @else  
        <li><a href="{{ url('/dashboard') }}">Dashboard</a></li>
        @permission('create-batch')
        <li><a href="{{ url('/batch/upload') }}">Batch Upload</a></li>
        @endpermission
        <li class="dropdown">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->name }} <span class="caret"></span></a>
            <ul class="dropdown-menu">
                @role('super-admin')
                <li><a href="{{ url('/admin') }}">Manage Accounts</a></li>
                <li role="separator" class="divider"></li>
                @endrole
                <li><a href="{{ url('/user/password/change') }}">Change Password</a></li>
                <li><a href="{{ url('/logout') }}">Logout</a></li>
            </ul>
        </li>
        @endif
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>