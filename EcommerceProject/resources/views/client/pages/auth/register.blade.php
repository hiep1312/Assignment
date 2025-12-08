@assets
    @vite('resources/css/auth.css')
@endassets

@script
<script>
    const PageController = {
        __proto__: window.BasePageController,

        init() {
            /* window.http.post(@js(route('api.auth.me'))).then(response => {
                if(response.data.user) {
                    window.location = @json(route('client.index'));
                }
            }); */

            super.init();
        },

        events: {
            "register:submit": async (event) => {
                try {
                    const { email, username, password, first_name, last_name, birthday, avatar } = event.detail;

                    const formData = new FormData();
                    formData.append('email', email);
                    formData.append('username', username);
                    formData.append('password', password);
                    formData.append('first_name', first_name);
                    formData.append('last_name', last_name);
                    if(birthday) formData.append('birthday', birthday);
                    if(avatar) formData.append('avatar', avatar);

                    const response = await window.http.post(@js(route('api.auth.register')), formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    document.dispatchEvent(new Event('register:success'));

                }catch(axiosError) {
                    const message = axiosError.response?.data?.message ?? axiosError.message;
                    const errors = axiosError.response?.data?.errors ?? {};

                    document.dispatchEvent(new CustomEvent('register:failed', {
                        detail: { message, errors }
                    }));
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
                    <h2>Sign Up</h2>
                    <p>Create your account</p>
                </div>

                <x-livewire-client::alert type="success" title="Registration Successful" icon="fas fa-check-circle"
                    x-data="{ showAlert: false, message: '' }"
                    x-init="
                        document.addEventListener('register:success', event => {
                            showAlert = true;
                            message = 'Account created successfully. Redirecting to login...';

                            setTimeout(() => {
                                window.location = '{{ route('login') }}';
                            }, 3000);
                        });
                    "
                    x-show="showAlert" wire:transition
                    style="margin-top: -15px;" wire:key="register-success-alert">

                    <span x-text="message"></span>

                    <x-slot:btn-close @click="showAlert = !showAlert"></x-slot:btn-close>
                </x-livewire-client::alert>

                <x-livewire-client::alert type="danger" title="Registration Failed" icon="fas fa-exclamation-triangle" x-data="{ showAlert: false, message: '' }"
                    x-init="document.addEventListener('register:failed', event => { showAlert = true; message = event.detail.message; })"
                    x-show="showAlert" wire:transition style="margin-top: -15px;" wire:key="register-failed-alert">

                    <span x-text="message"></span>

                    <x-slot:btn-close @click="showAlert = !showAlert"></x-slot:btn-close>
                </x-livewire-client::alert>


                <form class="auth-form" name="registerForm" x-data="{
                    email: '',
                    username: '',
                    password: '',
                    password_confirmation: '',
                    first_name: '',
                    last_name: '',
                    birthday: '',
                    avatar: null,
                    avatarPreview: null,
                    errors: {},
                    init() {
                        document.forms['registerForm'].addEventListener('submit', (event) => {
                            event.preventDefault();
                            this.errors = {};

                            // Validation
                            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                            if(!this.email.trim()) {
                                this.errors.email = 'Email is required.';
                            } else if(!emailRegex.test(this.email.trim())) {
                                this.errors.email = 'Please enter a valid email address.';
                            }

                            if(!this.username.trim()) {
                                this.errors.username = 'Username is required.';
                            } else if(this.username.trim().length < 3) {
                                this.errors.username = 'Username must be at least 3 characters.';
                            }

                            if(!this.password.trim()) {
                                this.errors.password = 'Password is required.';
                            } else if(this.password.trim().length < 8) {
                                this.errors.password = 'Password must be at least 8 characters.';
                            }

                            if(!this.password_confirmation.trim()) {
                                this.errors.password_confirmation = 'Please confirm your password.';
                            } else if(this.password !== this.password_confirmation) {
                                this.errors.password_confirmation = 'Passwords do not match.';
                            }

                            if(!this.first_name.trim()) {
                                this.errors.first_name = 'First name is required.';
                            }

                            if(!this.last_name.trim()) {
                                this.errors.last_name = 'Last name is required.';
                            }

                            if(!Object.keys(this.errors).length) {
                                document.dispatchEvent(new CustomEvent('register:submit', {
                                    detail: {
                                        email: this.email.trim(),
                                        username: this.username.trim(),
                                        password: this.password.trim(),
                                        first_name: this.first_name.trim(),
                                        last_name: this.last_name.trim(),
                                        birthday: this.birthday || null,
                                        avatar: this.avatar
                                    }
                                }));
                            }
                        });
                    },

                    handleAvatarChange(event) {
                        const file = event.target.files[0];
                        if(file) {
                            if(this.avatarPreview) {
                                URL.revokeObjectURL(this.avatarPreview);
                            }

                            this.avatar = file;
                            this.avatarPreview = URL.createObjectURL(file);
                        }
                    },

                    removeAvatar() {
                        this.avatar = null;
                        this.avatarPreview = null;
                        document.getElementById('avatar').value = '';
                    }
                }" novalidate>

                    <div class="auth-form-group text-center mb-4">
                        <div class="avatar-upload-wrapper" style="display: inline-block; position: relative;">
                            <div class="avatar-preview" style="width: 100px; height: 100px; border-radius: 50%; border: 3px dashed #ddd; margin: 0 auto; overflow: hidden; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                <template x-if="avatarPreview">
                                    <img :src="avatarPreview" id="avatarPreview" alt="Avatar preview" style="width: 100%; height: 100%; object-fit: cover;">
                                </template>
                                <template x-if="!avatarPreview">
                                    <i class="fas fa-user" style="font-size: 40px; color: #ccc;"></i>
                                </template>
                            </div>
                            <label for="avatar" x-show="!avatarPreview" class="auth-avatar-upload">
                                <i class="fas fa-camera" style="font-size: 14px;"></i>
                            </label>
                            <button type="button" x-show="avatarPreview" @click="removeAvatar" class="auth-avatar-remove">
                                <i class="fas fa-times" style="font-size: 12px;"></i>
                            </button>
                        </div>
                        <input type="file" id="avatar" accept="image/*" @change="handleAvatarChange" style="display: none;">
                        <small class="text-muted d-block mt-2">Optional: Upload your profile picture</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="auth-form-group">
                                <label for="email" class="auth-label">
                                    <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" id="email" x-model="email" class="auth-input" required
                                    placeholder="Enter your email">
                                <small :class="`invalid-feedback ${errors.email ? 'd-block' : ''}`" x-text="errors.email"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="auth-form-group">
                                <label for="username" class="auth-label">
                                    <i class="fas fa-user"></i> Username <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="username" x-model="username" class="auth-input" required
                                    placeholder="Enter your username">
                                <small :class="`invalid-feedback ${errors.username ? 'd-block' : ''}`" x-text="errors.username"></small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="auth-form-group">
                                <label for="first_name" class="auth-label">
                                    <i class="fas fa-user-circle"></i> First Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="first_name" x-model="first_name" class="auth-input" required
                                    placeholder="Enter your first name">
                                <small :class="`invalid-feedback ${errors.first_name ? 'd-block' : ''}`" x-text="errors.first_name"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="auth-form-group">
                                <label for="last_name" class="auth-label">
                                    <i class="fas fa-user-circle"></i> Last Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="last_name" x-model="last_name" class="auth-input" required
                                    placeholder="Enter your last name">
                                <small :class="`invalid-feedback ${errors.last_name ? 'd-block' : ''}`" x-text="errors.last_name"></small>
                            </div>
                        </div>
                    </div>

                    <div class="auth-form-group">
                        <label for="birthday" class="auth-label">
                            <i class="fas fa-calendar"></i> Birthday
                        </label>
                        <input type="date" id="birthday" x-model="birthday" class="auth-input"
                            placeholder="Select your birthday">
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="auth-form-group">
                                <label for="password" class="auth-label">
                                    <i class="fas fa-lock"></i> Password <span class="text-danger">*</span>
                                </label>
                                <div class="auth-password-wrapper" x-data="{ showPassword: false }">
                                    <input :type="showPassword ? 'text' : 'password'" id="password" x-model="password" class="auth-input" required
                                        placeholder="Enter your password">
                                    <button type="button" :class="`auth-toggle-password ${showPassword ? 'active' : ''}`"
                                        x-on:click="showPassword = !showPassword">
                                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                    </button>
                                </div>
                                <small :class="`invalid-feedback ${errors.password ? 'd-block' : ''}`" x-text="errors.password"></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="auth-form-group">
                                <label for="password_confirmation" class="auth-label">
                                    <i class="fas fa-lock"></i> Confirm Password <span class="text-danger">*</span>
                                </label>
                                <div class="auth-password-wrapper" x-data="{ showPassword: false }">
                                    <input :type="showPassword ? 'text' : 'password'" id="password_confirmation" x-model="password_confirmation" class="auth-input" required
                                        placeholder="Confirm your password">
                                    <button type="button" :class="`auth-toggle-password ${showPassword ? 'active' : ''}`"
                                        x-on:click="showPassword = !showPassword">
                                        <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                                    </button>
                                </div>
                                <small :class="`invalid-feedback ${errors.password_confirmation ? 'd-block' : ''}`" x-text="errors.password_confirmation"></small>
                            </div>
                        </div>
                    </div>

                    <div class="auth-checkbox-group">
                        <div>
                            <input type="checkbox" id="terms" class="form-check-input" required>
                            <label for="terms" class="form-check-label">
                                I agree to the <a href="javascript:void(0);" class="auth-link">Terms & Conditions</a>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn-primary">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>

                <div class="auth-divider">
                    <span>Or sign up with</span>
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
                    Already have an account? <a href="{{ route('login') }}" class="auth-link">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>
