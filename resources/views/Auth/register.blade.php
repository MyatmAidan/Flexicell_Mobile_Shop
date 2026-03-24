@extends('layouts.app_plane')
@section('title', 'Register Page')
@section('content')
    <div class="container h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card auth-card">
                    <div class="card-body p-5">
                        <div class="text-center">
                            <img src="{{ asset('img/flexicell_logo.png') }}" alt="Flexicell Logo" class="auth-logo">
                        </div>
                        <h3 class="auth-title mb-1">Adventure starts here</h3>
                        <p class="auth-subtitle mb-4">Make your app management easy and fun!</p>

                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            <!-- Name -->
                            <div class="mb-3">
                                <label class="form-label" for="name">Username</label>
                                <input id="name" type="text"
                                    class="form-control @error('name') is-invalid @enderror" name="name"
                                    value="{{ old('name') }}" placeholder="John Doe" autocomplete="name" autofocus>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" placeholder="john@example.com" autocomplete="email">
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group-merge">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        placeholder="············" autocomplete="new-password">
                                    <span class="password-toggle" onclick="togglePassword('password', 'toggleIcon1')">
                                        <i class="fa-solid fa-eye-slash" id="toggleIcon1"></i>
                                    </span>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <label class="form-label" for="password-confirm">Confirm Password</label>
                                <div class="input-group-merge">
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" placeholder="············" autocomplete="new-password">
                                    <span class="password-toggle" onclick="togglePassword('password-confirm', 'toggleIcon2')">
                                        <i class="fa-solid fa-eye-slash" id="toggleIcon2"></i>
                                    </span>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-2">
                                Sign up
                            </button>

                            <div class="text-center auth-footer">
                                <span>Already have an account? <a href="{{ route('login') }}">Sign in instead</a></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            }
        }
    </script>
@endsection
