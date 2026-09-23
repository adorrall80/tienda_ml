<x-layouts.auth title="Iniciar sesión">

    <h2 class="auth-title">Ingresa a tu cuenta</h2>

    <x-auth-session-status class="auth-status" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-input @error('email') form-input-error @enderror"
                   required autofocus autocomplete="username"
                   placeholder="tucorreo@ejemplo.com">
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Contraseña</label>
            <input id="password" type="password" name="password"
                   class="form-input @error('password') form-input-error @enderror"
                   required autocomplete="current-password"
                   placeholder="Tu contraseña">
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-row-between">
            <label class="form-check">
                <input type="checkbox" name="remember" id="remember_me">
                <span>Recordarme</span>
            </label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="form-link">¿Olvidaste tu contraseña?</a>
            @endif
        </div>

        <button type="submit" class="btn-auth">Ingresar</button>

        <p class="auth-footer-text">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}" class="form-link">Créala gratis</a>
        </p>
    </form>

</x-layouts.auth>
