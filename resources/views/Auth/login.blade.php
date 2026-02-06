@extends('layouts.app_plane')

@section('title', 'Login Page')

@section('content')
    <div class="container py-5">
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card " style="border-radius: 1rem;">
                    <div class="card-body p-5">
                        <h3 class="mb-4">Sign In</h3>
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            <!-- Email input -->
                            <div class=" mb-4">
                                <label class="form-label" for="email">Email address</label>
                                <input type="email" id="email" name="email"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus />
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Password input -->
                            <div class=" mb-4">
                                <label class="form-label" for="password">Password</label>
                                <input type="password" id="password" name="password"
                                    class="form-control form-control-lg @error('password') is-invalid @enderror" required
                                    autocomplete="current-password" />
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <!-- Checkbox -->
                            <div class="form-check d-flex justify-content-start mb-4">
                                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                    {{ old('remember') ? 'checked' : '' }} />
                                <label class="form-check-label ms-2" for="remember"> Remember password </label>
                            </div>

                            <button class="btn btn-primary btn-lg btn-block w-100" type="submit">Login</button>

                            <hr class="my-4">

                            <div class="text-center">
                                <p>Not a member? <a href="{{ route('register') }}">Register</a></p>
                            </div>
                            {{-- {!! JsValidator::formRequest('App\Http\Requests\MyFormRequest') !!} --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
