@push('styles')
    <link rel="stylesheet" href="{{ asset('admin/assets/css/pages/page-auth.css') }}" />
@endpush

<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
            <!-- Register -->
            <div class="card">
                <div class="card-body">
                    <!-- Logo -->
                    <div class="app-brand justify-content-center" style="margin-bottom: 2rem;">
                        <a href="{{ route('template.admin.index') }}" class="app-brand-link gap-2">
                            <img src="{{ asset("storage/logo-bookio.webp") }}" alt="Logo website" width="45" height="45">
                            <span class="app-brand-text demo text-body fw-bolder" style="text-transform: none">Bookio</span>
                        </a>
                    </div>
                    <!-- /Logo -->
                    <h4 class="mb-2">Welcome to Bookio! 👋</h4>
                    <p class="mb-4">Please log in to access the website’s admin dashboard.</p>

                    <form id="formAuthentication" class="mb-3" wire:submit.prevent="handleLogin" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Email or Username</label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" wire:model="username"
                                placeholder="Enter your email or username" autofocus />
                            @error('username')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 form-password-toggle">
                            <div class="d-flex justify-content-between">
                                <label class="form-label" for="password">Password</label>
                                <a href="{{ route('template.auth-forgot-password-basic') }}" tabindex="-1">
                                    <small>Forgot Password?</small>
                                </a>
                            </div>
                            <div class="input-group input-group-merge">
                                <input type="password" id="password" class="form-control @error('password') is-invalid @enderror" wire:model="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password" />
                                <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input @error('remember') is-invalid @enderror" type="checkbox" id="remember-me" wire:model="remember" />
                                <label class="form-check-label" for="remember-me"> Remember me</label>
                                @error('remember')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                        </div>
                    </form>
                    @error('auth')
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="close"></button>
                        </div>
                    @enderror
                </div>
            </div>
            <!-- /Register -->
        </div>
    </div>
</div>
