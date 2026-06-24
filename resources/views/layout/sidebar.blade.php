<nav class="sidebar">
  <div class="sidebar-header">
    <a href="{{ url('/') }}" class="sidebar-brand" style="display: flex; align-items: center;">
      {{-- <img src="{{ url(asset('/assets/images/terranova.png')) }}" alt="Logo"
                style="height: 18px; width: auto; margin-right: 8px;"> --}}
      <span><span style="color: #323232;">TERRA</span><span style="color: #3f5981;">NOVA</span></span>
    </a>
    <div class="sidebar-toggler not-active">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>
  <div class="sidebar-body">
    <ul class="nav">
      <li class="nav-item {{ active_class(['/']) }}">
        <a href="{{ url('/') }}" class="nav-link">
          <i class="link-icon" data-feather="box"></i>
          <span class="link-title">Inicio</span>
        </a>
      </li>
      <li class="nav-item {{ active_class(['residenciales', 'residenciales/*']) }}">
        <a href="{{ url('/residenciales') }}" class="nav-link">
          <i class="link-icon" data-feather="home"></i>
          <span class="link-title">Residenciales</span>
        </a>
      </li>
      <li class="nav-item {{ active_class(['clientes', 'clientes/*']) }}">
        <a href="{{ url('/clientes') }}" class="nav-link">
          <i class="link-icon" data-feather="users"></i>
          <span class="link-title">Clientes</span>
        </a>
      </li>
      <li class="nav-item {{ active_class(['ventas', 'ventas/*']) }}">
        <a href="{{ url('/ventas') }}" class="nav-link">
          <i class="link-icon" data-feather="dollar-sign"></i>
          <span class="link-title">Ventas</span>
        </a>
      </li>
      <li class="nav-item {{ active_class(['vender', 'vender/*']) }}">
        <a href="{{ url('/vender') }}" class="nav-link">
          <i class="link-icon" data-feather="shopping-cart"></i>
          <span class="link-title">Vender</span>
        </a>
      </li>
    </ul>
  </div>
</nav>
