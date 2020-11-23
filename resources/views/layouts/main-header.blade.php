<a href="/absensi/beranda" class="navbar-brand">
  <img src="{{ url('images/logoIPApp.png') }}" alt="INDONESIA POWER Logo" class="brand-image">
  <span div class="d-none d-md-inline">PBJ App</span>
  <!-- <span class="brand-text font-weight-light">AdminLTE 3</span> -->
</a>
<!-- <div class="navbar-collapse"> -->
<ul class="navbar-nav">
  <li class="nav-item">
    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
  </li>
</ul>
<!-- </div> -->

<ul class="nav navbar-nav navbar-no-expand ml-auto">

  <li class="nav-item dropdown user user-menu ">

    <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">

      <img src="{{ url('AdminLTE/dist/img/avatar.png') }}" class="user-image" alt="User Image">
      <span>Administrator</span>
      <i class="fa fa-caret-down"></i>
    </a>
    <ul class="dropdown-menu">
      <li class="user-header">
        <img src="{{ url('AdminLTE/dist/img/avatar.png') }}" class="img-circle" alt="User Image">
        <p>Selamat Datang Administrator
        </p>
      </li>

      <li class="user-footer ">
        <div class="row">
          <div class="col-12">
          <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="btn btn-danger  btn-flat" type="submit">Logout</button>
            </form>
          </div>
        </div>
      </li>
    </ul>
  </li>
</ul>