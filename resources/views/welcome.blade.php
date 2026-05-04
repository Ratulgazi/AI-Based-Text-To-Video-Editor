<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Premium Text-to-Video Generator 🎬</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 text-white font-['Inter']" style="background-image: linear-gradient(rgba(15, 23, 42, 0.8), rgba(15, 23, 42, 0.6)), url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'); background-size: cover; background-position: center; background-attachment: fixed;">
    <div class="container mx-auto px-4 py-16">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-4 drop-shadow-lg">
                Premium Text-to-Video Generator 🎬
            </h1>
        </div>

        @if(Auth::check())
            <!-- Authenticated User Section -->
            <div class="max-w-4xl mx-auto">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 shadow-2xl border border-white/20">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-semibold text-blue-300 mb-2">Welcome back, {{ Auth::user()->name }}!</h2>
                        <p class="text-gray-300">Create professional videos from your text instantly</p>
                    </div>

                    @if(session('success'))
                        <div class="bg-green-500/20 border border-green-400 text-green-100 px-4 py-3 rounded-lg mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-500/20 border border-red-400 text-red-100 px-4 py-3 rounded-lg mb-6">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Video Generation Form -->
                    <form method="POST" action="/generate" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        <div>
                            <label for="text" class="block text-sm font-medium text-gray-300 mb-2">
                                Enter your text content
                            </label>
                            <textarea
                                id="text"
                                name="text"
                                rows="6"
                                class="w-full px-4 py-3 bg-white/90 text-gray-900 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                                placeholder="Describe the video you want to create..."
                            ></textarea>
                        </div>

                        <div>
                            <label for="background" class="block text-sm font-medium text-gray-300 mb-2">
                                Optional Background Image
                            </label>
                            <input
                                type="file"
                                id="background"
                                name="background"
                                accept="image/*"
                                class="w-full px-4 py-3 bg-white/90 text-gray-900 rounded-lg border border-gray-300 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            >
                        </div>

                        <div class="text-center">
                            <button
                                type="submit"
                                class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transform hover:scale-105 transition-all duration-200 shadow-lg"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                Generate Video
                            </button>
                        </div>
                    </form>

                    @if(session('video'))
                        <!-- Video Display -->
                        <div class="mt-12 text-center">
                            <h3 class="text-2xl font-semibold text-green-300 mb-6">🎉 Your Video is Ready!</h3>
                            <div class="bg-black/50 rounded-xl p-6 inline-block">
                                <video
                                    width="640"
                                    controls
                                    class="rounded-lg shadow-2xl max-w-full h-auto"
                                    style="max-width: 100%; height: auto;"
                                >
                                    <source src="{{ asset(session('video')) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            <div class="mt-6">
                                <a
                                    href="{{ asset(session('video')) }}"
                                    download
                                    class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Download Video
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Logout Button -->
                    <div class="text-center mt-12 pt-8 border-t border-white/20">
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors duration-200"
                            >
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                </svg>
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <!-- Guest Section -->
            <div class="max-w-md mx-auto">
                <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 shadow-2xl border border-white/20 text-center">
                    <p class="text-xl text-gray-300 mb-8">
                        Register or login to generate your first professional video
                    </p>

                    <div class="space-y-4">
                        <button
                            onclick="showForm('login')"
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-yellow-500 text-gray-900 font-semibold rounded-lg hover:bg-yellow-400 transition-colors duration-200"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Login
                        </button>

                        <button
                            onclick="showForm('register')"
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-green-500 text-gray-900 font-semibold rounded-lg hover:bg-green-400 transition-colors duration-200"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Register
                        </button>
                    </div>
                </div>
            </div>

            <!-- Login Modal -->
            <div id="login-form" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center hidden z-50">
                <div class="bg-white/95 rounded-2xl p-8 shadow-2xl border border-white/20 max-w-md w-full mx-4">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-semibold text-gray-900">Login</h3>
                        <button onclick="closeForm()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('login') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="login" class="block text-sm font-medium text-gray-700 mb-1">Email or Phone</label>
                            <input
                                id="login"
                                type="text"
                                name="login"
                                value="{{ old('login') }}"
                                required
                                autocomplete="username"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('login')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <input
                                id="remember"
                                type="checkbox"
                                name="remember"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700">Remember me</label>
                        </div>

                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-yellow-500 text-gray-900 font-semibold rounded-lg hover:bg-yellow-400 transition-colors duration-200"
                        >
                            Login
                        </button>

                        <div class="text-center">
                            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">Forgot password?</a>
                        </div>
                    </form>

                    <div class="mt-6">
                        <a
                            href="{{ route('login.google') }}"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200"
                        >
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                <path fill="currentColor" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                                <path fill="currentColor" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                                <path fill="currentColor" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                                <path fill="currentColor" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                            </svg>
                            Login with Google
                        </a>
                    </div>
                </div>
            </div>

            <!-- Register Modal -->
            <div id="register-form" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center hidden z-50">
                <div class="bg-white/95 rounded-2xl p-8 shadow-2xl border border-white/20 max-w-md w-full mx-4">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-semibold text-gray-900">Register</h3>
                        <button onclick="closeForm()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form method="POST" action="{{ route('register') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input
                                id="name"
                                type="text"
                                name="name"
                                value="{{ old('name') }}"
                                required
                                autocomplete="name"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="reg_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input
                                id="reg_email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone (optional)</label>
                            <input
                                id="phone"
                                type="text"
                                name="phone"
                                value="{{ old('phone') }}"
                                autocomplete="tel"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="reg_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                            <input
                                id="reg_password"
                                type="password"
                                name="password"
                                required
                                autocomplete="new-password"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                                class="w-full px-4 py-3 bg-white border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                            @error('password_confirmation')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <button
                            type="submit"
                            class="w-full inline-flex items-center justify-center px-6 py-3 bg-green-500 text-gray-900 font-semibold rounded-lg hover:bg-green-400 transition-colors duration-200"
                        >
                            Register
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>

    <script>
        function showForm(type) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');

            if (type === 'login') {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
            } else if (type === 'register') {
                registerForm.classList.remove('hidden');
                loginForm.classList.add('hidden');
            }
        }

        function closeForm() {
            document.getElementById('login-form').classList.add('hidden');
            document.getElementById('register-form').classList.add('hidden');
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            const loginForm = document.getElementById('login-form');
            const registerForm = document.getElementById('register-form');

            if (event.target === loginForm) {
                closeForm();
            }
            if (event.target === registerForm) {
                closeForm();
            }
        });

        // Validation for registration form
        document.addEventListener('DOMContentLoaded', function() {
            const registerForm = document.querySelector('#register-form form');
            if (registerForm) {
                registerForm.addEventListener('submit', function(e) {
                    const email = document.getElementById('reg_email').value.trim();
                    if (!email) {
                        e.preventDefault();
                        alert('Please provide your email address to register.');
                        return false;
                    }
                });
            }
        });
    </script>
</body>
</html>