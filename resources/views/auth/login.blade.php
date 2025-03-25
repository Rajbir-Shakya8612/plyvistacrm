@extends('layouts.auth')

@section('content')
<div class="container">
    <div class="row justify-content-center min-vh-100 align-items-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" height="60">
                        <h4 class="mt-3">Welcome Back</h4>
                        <p class="text-muted">Please login to your account</p>
                    </div>

                    <form id="loginForm" class="needs-validation" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                            <div class="invalid-feedback">Please enter your email address.</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">Please enter your password.</div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="loginButton">
                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            Login
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p class="mb-0">Don't have an account? <a href="{{ route('register') }}">Register</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/services/auth.js') }}"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const loginButton = document.getElementById('loginButton');
    const spinner = loginButton.querySelector('.spinner-border');
    const togglePassword = document.getElementById('togglePassword');

    // Toggle password visibility
    togglePassword.addEventListener('click', function() {
        const input = this.previousElementSibling;
        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
        input.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    loginForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        if (!loginForm.checkValidity()) {
            e.stopPropagation();
            loginForm.classList.add('was-validated');
            return;
        }

        // Disable button and show spinner
        loginButton.disabled = true;
        spinner.classList.remove('d-none');

        try {
            const formData = new FormData(loginForm);
            const credentials = Object.fromEntries(formData);
            
            // First try web authentication
            try {
                const response = await axios.post('/login', credentials, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.data.success) {
                    const user = response.data.user;
                    
                    // Show success message
                    await Swal.fire({
                        icon: 'success',
                        title: 'Login Successful',
                        text: 'Redirecting to dashboard...',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Role-based redirection
                    switch(user.role) {
                        case 'admin':
                            window.location.href = '/admin/dashboard';
                            break;
                        case 'salesperson':
                            window.location.href = '/salesperson/dashboard';
                            break;
                        case 'dealer':
                            window.location.href = '/dealer/dashboard';
                            break;
                        case 'carpenter':
                            window.location.href = '/carpenter/dashboard';
                            break;
                        default:
                            window.location.href = '/dashboard';
                    }
                }
            } catch (webError) {
                // If web auth fails, try API authentication
                const apiResponse = await window.authService.login(credentials);
                
                if (apiResponse.success) {
                    const user = apiResponse.user;
                    
                    await Swal.fire({
                        icon: 'success',
                        title: 'Login Successful',
                        text: 'Redirecting to dashboard...',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    // Role-based API redirection
                    switch(user.role) {
                        case 'admin':
                            window.location.href = '/api/admin/dashboard';
                            break;
                        case 'salesperson':
                            window.location.href = '/api/salesperson/dashboard';
                            break;
                        case 'dealer':
                            window.location.href = '/api/dealer/dashboard';
                            break;
                        case 'carpenter':
                            window.location.href = '/api/carpenter/dashboard';
                            break;
                        default:
                            window.location.href = '/api/dashboard';
                    }
                }
            }
        } catch (error) {
            console.error('Login error:', error);
            const message = error.response?.data?.message || error.message || 'Login failed. Please try again.';
            
            Swal.fire({
                icon: 'error',
                title: 'Login Failed',
                text: message,
                confirmButtonColor: '#3085d6'
            });
        } finally {
            // Enable button and hide spinner
            loginButton.disabled = false;
            spinner.classList.add('d-none');
        }
    });
});
</script>
@endpush 