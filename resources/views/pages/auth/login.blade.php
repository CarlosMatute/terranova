@extends('layout.master2')

@section('content')
<div class="page-content d-flex align-items-center justify-content-center px-3 px-md-0" style="background: linear-gradient(135deg, var(--ins-azul-oscuro) 0%, var(--ins-azul) 50%, var(--ins-azul-claro) 100%);">

  <div class="row w-100 mx-0 auth-page">
    <div class="col-12 col-sm-10 col-md-8 col-xl-6 mx-auto">
      <div class="card border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
        <div class="row g-0">
          <div class="col-md-4 d-none d-md-block">
            <div class="auth-side-wrapper h-100" style="background-image: url({{ url('https://via.placeholder.com/219x452') }}); min-height: 452px;">
              <img src="{{ url(asset('/assets/images/poster_login.png')) }}" alt="Avatar" class="auth-side-wrapper h-100" style="object-fit: cover;" onerror="this.onerror=null; this.src='{{ url(asset('/assets/images/user2-403d6e88.png')) }}';" />
            </div>
          </div>
          <div class="col-md-8" style="background: var(--ins-blanco-humo);">
            <div class="auth-form-wrapper px-3 px-md-5 py-4 py-md-5">
              <a href="#" class="noble-ui-logo d-block mb-2" style="font-family: 'ND LOGOS REGULAR', sans-serif; text-shadow: 0 0 0.3px currentColor; -webkit-text-stroke: 0.6px currentColor; font-weight: 800; color: #323232; font-size: 22px;">
                <span style="color: #323232;">TERRA</span><span style="color: #3f5981;">NOVA</span><span style="color: #3f5981; font-weight: 300;">1.0</span>
              </a>
              <h5 class="fw-normal mb-4" style="color: var(--ins-negro); font-size: 1rem;">¡Bienvenido! Inicia sesión en tu cuenta.</h5>
              <form class="forms-sample" action="{{ route('login') }}" method="POST">
                @csrf
                <div class="mb-3">
                  <label for="userEmail" class="form-label" style="color: var(--ins-negro);">Usuario o Correo Electrónico</label>
                  <input type="text" class="form-control" name="email" id="userEmail" placeholder="Escribe aquí..." style="border-color: var(--ins-azul-claro);" required>
                </div>
                <div class="mb-3">
                  <label for="userPassword" class="form-label" style="color: var(--ins-negro);">Contraseña</label>
                  <input type="password" class="form-control" name="password" id="userPassword" autocomplete="current-password" placeholder="Escribe aquí..." style="border-color: var(--ins-azul-claro);" required>
                </div>
                @error('error')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                <div>
                  <button type="submit" class="btn btn-azul w-100 w-md-auto me-2 mb-2 mb-md-0" style="border-radius: 8px;">
                    <i class="btn-icon-prepend" data-feather="log-in" width="16" height="16"></i> Iniciar sesión
                  </button>
                </div>
              </form>
              <div class="text-center mt-4">
                <img src="{{ asset('/assets/images/terranova_logo.png') }}" alt="" style="width: 100%; height: auto; max-width: 240px;">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
@endsection