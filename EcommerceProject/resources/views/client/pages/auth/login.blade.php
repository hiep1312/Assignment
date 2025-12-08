@assets
    @vite('resources/css/auth.css')
@endassets

@script
<script>
    const PageController = {
        __proto__: window.BasePageController,

        init() {
            window.http.post(@js(route('api.auth.me'))).then(response => {
                if(response.data.user) {
                    window.location = @json(route('client.index'));
                }
            });

            super.init();
        },

        events: {
            "login:submit": async (event) => {
                try {
                    const { username, password, remember } = event.detail;

                    const response = await window.http.post(@js(route('api.auth.login')), { username, password });

                    const { data: axiosData } = response;

                    if(remember) {
                        window.setCookie('auth_token', axiosData.token);
                    }else {
                        localStorage.setItem('auth_token', axiosData.token);
                    }

                    document.dispatchEvent(new Event('login:success'));

                }catch(axiosError) {
                    const message = axiosError.response?.data?.message ?? axiosError.message;

                    document.dispatchEvent(new CustomEvent('login:failed', { detail: { message } }));
                }
            },
        }
    };

    PageController.init();
</script>
@endscript
<div class="container-xl my-5" id="main-component">
    <div class="auth-wrapper">
        <div class="auth-left-side">
            <div class="auth-brand">
                <div class="auth-logo-icon">
                    <img src="{{ asset("storage/logo-bookio.webp") }}" alt="Logo website" style="width: 100%; height: 100%;">
                </div>
                <h1 class="auth-brand-title">Bookio</h1>
                <p class="auth-brand-subtitle">Your Modern Bookstore Platform</p>
            </div>
            <div class="auth-benefits">
                <div class="auth-benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Secure & Reliable</span>
                </div>
                <div class="auth-benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <span>User-Friendly Interface</span>
                </div>
                <div class="auth-benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <span>24/7 Customer Support</span>
                </div>
            </div>
        </div>

        <div class="auth-right-side">
            <div class="auth-form-wrapper">
                <div class="auth-form-header">
                    <h2>Login</h2>
                    <p>Welcome back</p>
                </div>

                <x-livewire-client::alert type="success" title="Login Successful" icon="fas fa-check-circle"
                    x-data="{ showAlert: false, message: '' }"
                    x-init="
                        document.addEventListener('login:success', event => {
                            showAlert = true;
                            message = 'You have logged in successfully. Redirecting...';

                            setTimeout(() => {
                                window.location = '{{ route('client.index') }}';
                            }, 3000);
                        });
                    "
                    x-show="showAlert" wire:transition
                    style="margin-top: -15px;" wire:key="login-success-alert">

                    <span x-text="message"></span>

                    <x-slot:btn-close @click="showAlert = !showAlert"></x-slot:btn-close>
                </x-livewire-client::alert>

                <x-livewire-client::alert type="danger" title="Login Failed" icon="fas fa-exclamation-triangle" x-data="{ showAlert: false, message: '' }"
                    x-init="document.addEventListener('login:failed', event => { showAlert = true; message = event.detail.message; })"
                    x-show="showAlert" wire:transition style="margin-top: -15px;" wire:key="login-failed-alert">

                    <span x-text="message"></span>

                    <x-slot:btn-close @click="showAlert = !showAlert"></x-slot:btn-close>
                </x-livewire-client::alert>


                <form class="auth-form" name="loginForm" x-data="{
                    username: '',
                    password: '',
                    remember: false,
                    errors: {},
                    init() {
                        document.forms['loginForm'].addEventListener('submit', (event) => {
                            event.preventDefault();
                            this.errors = {};

                            const trimmedUsername = this.username.trim();
                            const trimmedPassword = this.password.trim();

                            if(!trimmedUsername.length) {
                                this.errors.username = 'Username is required.';
                            }

                            if(!trimmedPassword.length) {
                                this.errors.password = 'Password is required.';
                            }else if(trimmedPassword.length < 8) {
                                this.errors.password = 'Password must be at least 8 characters.';
                            }

                            if(!Object.keys(this.errors).length) {
                                document.dispatchEvent(new CustomEvent('login:submit', { detail: { username: trimmedUsername, password: trimmedPassword, remember: Boolean(this.remember) } }));
                            }
                        });
                    }
                }" novalidate>
                    <div class="auth-form-group">
                        <label for="username" class="auth-label">
                            <i class="fas fa-envelope"></i> Username
                        </label>
                        <input type="text" id="username" x-model="username" class="auth-input" required
                            placeholder="Enter your username">
                        <small :class="`invalid-feedback ${errors.username ? 'd-block' : ''}`" x-text="errors.username"></small>
                    </div>

                    <div class="auth-form-group">
                        <label for="password" class="auth-label">
                            <i class="fas fa-lock"></i> Password
                        </label>
                        <div class="auth-password-wrapper" x-data="{ showPassword: false }">
                            <input :type="showPassword ? 'text' : 'password'" id="password" x-model="password" class="auth-input" required
                                placeholder="Enter your password">
                            <button type="button" :class="`auth-toggle-password ${showPassword ? 'active' : ''}`" id="togglePassword"
                                x-on:click="showPassword = !showPassword">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                        <small :class="`invalid-feedback ${errors.password ? 'd-block' : ''}`" x-text="errors.password"></small>
                    </div>

                    <div class="auth-checkbox-group">
                        <div>
                            <input type="checkbox" id="remember" x-model="remember" class="form-check-input">
                            <label for="remember" class="form-check-label">Remember me</label>
                        </div>
                        <a href="javascript:void(0);" class="auth-forgot-password">Forgot password?</a>
                    </div>

                    <button type="submit" class="auth-btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>

                <div class="auth-divider">
                    <span>Or</span>
                </div>

                <div class="auth-social-login">
                    <button class="auth-btn-social" title="Google">
                        <i class="fab fa-google"></i>
                    </button>
                    <button class="auth-btn-social" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </button>
                    <button class="auth-btn-social" title="GitHub">
                        <i class="fab fa-github"></i>
                    </button>
                </div>

                <p class="auth-signup-text">
                    Don’t have an account? <a href="{{ route('register') }}" class="auth-link">Sign up now</a>
                </p>
            </div>
        </div>
    </div>
</div>
