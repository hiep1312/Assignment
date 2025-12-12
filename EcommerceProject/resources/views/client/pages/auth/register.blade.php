@assets
    @vite('resources/css/auth.css')
@endassets

@script
<script>
    const PageController = {
        __proto__: window.BasePageController,

        init() {
            window.http.get(@js(route('api.auth.me'))).then(response => {
                if(response.data.user) {
                    window.location = @json(route('client.index'));
                }
            }).catch(() => {});

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

                    document.dispatchEvent(new CustomEvent('register:success', { detail: { email, password } }));

                }catch(axiosError) {
                    if(axiosError.status === 422) {
                        const errors = axiosError.response?.data?.errors ?? {};
                        const formattedErrors = {};

                        for(const field in errors) {
                            formattedErrors[field] = errors[field][0];
                        }

                        document.dispatchEvent(new CustomEvent('register:errors', { detail: { errors }}));
                    }else {
                        const message = axiosError.response?.data?.message ?? axiosError.message;

                        document.dispatchEvent(new CustomEvent('register:failed', { detail: { message }}));
                    }
                }
            },
        }
    };

    PageController.init();
</script>
@endscript
<div class="container-xl my-5" id="main-component">
    <div class="auth-wrapper">
        <div class="auth-right-side wow fadeInUp" data-wow-delay="0.1s">
            <div class="auth-form-wrapper">
                <div class="auth-form-header">
                    <h2>Sign Up</h2>
                    <p>Create your account</p>
                </div>

                <x-livewire-client::alert type="success" title="Registration Successful" icon="fas fa-check-circle"
                    x-data="{ showAlert: false, message: '' }"
                    x-init="
                        document.addEventListener('register:success', event => {
                            let countdown = 3;
                            showAlert = true;
                            message = `Account created successfully. Redirecting to login page in ${countdown}s...`;

                            const interval = setInterval(() => {
                                if(countdown > 0) {
                                    countdown--;
                                    message = `Account created successfully. Redirecting to login page in ${countdown}s...`;
                                }else {
                                    clearInterval(interval);
                                    const { email, password } = event.detail;
                                    localStorage.setItem('_username', email);
                                    localStorage.setItem('_password', password);
                                    window.location = '{{ route('login') }}';
                                }
                            }, 1000);
                        });
                    "
                    x-show="showAlert" wire:transition style="margin-top: -15px;" wire:key="register-success-alert">

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
                    birthday: null,
                    avatar: null,
                    avatarPreview: null,
                    errors: {},
                    init() {
                        document.addEventListener('register:errors', event => {
                            this.errors = event.detail.errors;
                        });

                        document.forms['registerForm'].addEventListener('submit', event => {
                            event.preventDefault();
                            this.errors = {};

                            const normalizedForm = {
                                email: this.email.trim(),
                                username: this.username.trim(),
                                password: this.password.trim(),
                                password_confirmation: this.password_confirmation.trim(),
                                first_name: this.first_name.trim(),
                                last_name: this.last_name.trim(),
                                birthday: this.birthday || null,
                                avatar: this.avatar
                            };

                            const regexRules = {
                                email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
                                birthday: /^\d{4}-\d{2}-\d{2}$/,
                            };

                            if(!normalizedForm.email) {
                                this.errors.email = 'Email is required.';
                            }else if(!regexRules.email.test(normalizedForm.email)) {
                                this.errors.email = 'Please enter a valid email address.';
                            }

                            if(!normalizedForm.username) {
                                this.errors.username = 'Username is required.';
                            }else if(normalizedForm.username.length < 5) {
                                this.errors.username = 'Username must be at least 5 characters.';
                            }

                            if(!normalizedForm.password) {
                                this.errors.password = 'Password is required.';
                            }else if(normalizedForm.password.length < 8) {
                                this.errors.password = 'Password must be at least 8 characters.';
                            }

                            if(!normalizedForm.password_confirmation) {
                                this.errors.password_confirmation = 'Please confirm your password.';
                            }else if(normalizedForm.password !== normalizedForm.password_confirmation) {
                                this.errors.password_confirmation = 'Passwords do not match.';
                            }

                            if(!normalizedForm.first_name) {
                                this.errors.first_name = 'First name is required.';
                            }

                            if(!normalizedForm.last_name) {
                                this.errors.last_name = 'Last name is required.';
                            }

                            if(normalizedForm.birthday !== null && !regexRules.birthday.test(normalizedForm.birthday)) {
                                this.errors.birthday = 'Please enter a valid date in the format YYYY-MM-DD.';
                            }

                            if(normalizedForm.avatar !== null) {
                                const maxSize = 10 * 1024 * 1024;

                                if(!normalizedForm.avatar.type.includes('image/')) {
                                    this.errors.avatar = 'Please upload a valid image file.';
                                }else if(normalizedForm.avatar.size > maxSize) {
                                    this.errors.avatar = 'Image size must be less than 10MB.';
                                }
                            }

                            if(!Object.keys(this.errors).length) {
                                document.dispatchEvent(new CustomEvent('register:submit', { detail: normalizedForm }));
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
                }" novalidate enctype="multipart/form-data">
                    <div class="auth-form-group text-center">
                        <div class="d-inline-block position-relative">
                            <div class="avatar-preview">
                                <template x-if="avatarPreview">
                                    <img :src="avatarPreview" id="avatarPreview" alt="Avatar preview" style="width: 100%; height: 100%; object-fit: cover;">
                                </template>

                                <template x-if="!avatarPreview">
                                    <i class="fas fa-user" style="font-size: 40px; color: #ccc;"></i>
                                </template>
                            </div>

                            <label for="avatar" x-show="!avatarPreview" class="auth-avatar-upload">
                                <i class="fas fa-camera"></i>
                            </label>

                            <button type="button" x-show="avatarPreview" @click="removeAvatar" class="auth-avatar-remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <input type="file" id="avatar" accept="image/*" @change="handleAvatarChange" style="display: none;">
                        <small :class="`invalid-feedback mt-2 ${errors.avatar ? 'd-block text-center' : ''}`" :style="{ marginBottom: '-.25rem' }" x-text="errors.avatar"></small>
                        <small class="text-muted d-block mt-2">Optional: Upload your profile picture</small>
                    </div>

                    <div class="row">
                        <div class="col-sm-6 col-md-12 col-lg-6">
                            <div class="auth-form-group">
                                <label for="email" class="auth-label">
                                    <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" id="email" x-model="email" class="auth-input" required
                                    placeholder="Enter your email">
                                <small :class="`invalid-feedback ${errors.email ? 'd-block' : ''}`" x-text="errors.email"></small>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-12 col-lg-6">
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
                        <div class="col-sm-6 col-md-12 col-lg-6">
                            <div class="auth-form-group">
                                <label for="first_name" class="auth-label">
                                    <i class="fas fa-user-circle"></i> First Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" id="first_name" x-model="first_name" class="auth-input" required
                                    placeholder="Enter your first name">
                                <small :class="`invalid-feedback ${errors.first_name ? 'd-block' : ''}`" x-text="errors.first_name"></small>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-12 col-lg-6">
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
                        <small :class="`invalid-feedback ${errors.birthday ? 'd-block' : ''}`" x-text="errors.birthday"></small>
                    </div>

                    <div class="row" x-data="{ showPassword: false }">
                        <div class="col-sm-6 col-md-12 col-lg-6">
                            <div class="auth-form-group">
                                <label for="password" class="auth-label">
                                    <i class="fas fa-lock"></i> Password <span class="text-danger">*</span>
                                </label>
                                <div class="auth-password-wrapper">
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
                        <div class="col-sm-6 col-md-12 col-lg-6">
                            <div class="auth-form-group">
                                <label for="password_confirmation" class="auth-label">
                                    <i class="fas fa-lock"></i> Confirm Password <span class="text-danger">*</span>
                                </label>
                                <div class="auth-password-wrapper" >
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
        <div class="auth-left-side auth-left-side-register wow fadeInUp" data-wow-delay="0.5s">
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
    </div>
</div>
