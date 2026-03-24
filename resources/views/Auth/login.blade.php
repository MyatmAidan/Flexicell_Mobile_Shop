@extends('layouts.app_plane')

@section('title', 'Login Page')

@section('content')
    <div class="container h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card auth-card">
                    <div class="card-body p-5">
                        <div class="text-center">
                            <img src="{{ asset('img/flexicell_logo.png') }}" alt="Flexicell Logo" class="auth-logo">
                        </div>
                        <h3 class="auth-title mb-1">Welcome!</h3>
                        <p class="auth-subtitle mb-4">Please sign-in to your account and start the adventure</p>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <!-- Email input -->
                            <div class="mb-3">
                                <label class="form-label" for="email">Email</label>
                                <input type="email" id="email" name="email"
                                    class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                    placeholder="Enter your email" required autocomplete="email" autofocus />
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Password input -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                    {{-- <a href="#">
                                        <small>Forgot Password?</small>
                                    </a> --}}
                                </div>
                                <div class="input-group-merge">
                                    <input type="password" id="password" name="password"
                                        class="form-control @error('password') is-invalid @enderror"
                                        placeholder="············" required autocomplete="current-password" />
                                    <span class="password-toggle" onclick="togglePassword()">
                                        <i class="fa-solid fa-eye-slash" id="toggleIcon"></i>
                                    </span>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Remember Me -->
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                        {{ old('remember') ? 'checked' : '' }} />
                                    <label class="form-check-label" for="remember"> Remember Me </label>
                                </div>
                            </div>

                            <button class="btn btn-primary w-100 mb-2" type="submit">Sign in</button>

                            <div class="text-center auth-footer">
                                <span>New on our platform? <a href="{{ route('register') }}">Create an account</a></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
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
