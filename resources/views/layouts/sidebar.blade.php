<!-- Sidebar Menu -->
<nav class="mt-4">
  <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
    <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
    <li class="nav-item">
      <a href="{{url("/")}}" class="{{ (request()->segment(1) == '') ? 'nav-link active' : 'nav-link inactive' }}">
        <i class="nav-icon fas fa-table"></i>
        <p>
          Daftar User
        </p>
      </a>
    </li>
    <li class="nav-item">
      <a href="{{url("/admin/step")}}" class="{{ (request()->segment(2) == 'step') ? 'nav-link active' : 'nav-link inactive' }}">
        <i class="nav-icon fas fa-clock"></i>
        <p>
          SLA 
        </p>
      </a>
    </li>
  </ul>
</nav>
<!-- /.sidebar-menu -->