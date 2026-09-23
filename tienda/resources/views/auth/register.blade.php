<x-layouts.auth title="Crear cuenta">

    <h2 class="auth-title">Crea tu cuenta gratis</h2>

    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Nombre completo</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   class="form-input @error('name') form-input-error @enderror"
                   required autofocus autocomplete="name"
                   placeholder="Tu nombre">
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   class="form-input @error('email') form-input-error @enderror"
                   required autocomplete="username"
                   placeholder="tucorreo@ejemplo.com">
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Contraseña</label>
            <input id="password" type="password" name="password"
                   class="form-input @error('password') form-input-error @enderror"
                   required autocomplete="new-password"
                   placeholder="Mínimo 8 caracteres">
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-input"
                   required autocomplete="new-password"
                   placeholder="Repite tu contraseña">
        </div>

        <button type="submit" class="btn-auth">Crear cuenta</button>

        <p class="auth-footer-text">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}" class="form-link">Ingresa aquí</a>
        </p>
    </form>

</x-layouts.auth>
