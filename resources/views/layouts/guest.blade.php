<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'HRMS Login') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Cleanup Stale Service Workers -->
        <script>
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.getRegistrations().then(function(registrations) {
                    for(let registration of registrations) {
                        registration.unregister();
                    }
                });
            }
        </script>
        
        <style>
            body { font-family: 'Inter', sans-serif; }
            
            /* Background Animation */
            @keyframes blob {
                0% { transform: translate(0px, 0px) scale(1); }
                33% { transform: translate(30px, -50px) scale(1.1); }
                66% { transform: translate(-20px, 20px) scale(0.9); }
                100% { transform: translate(0px, 0px) scale(1); }
            }
            .animate-blob {
                animation: blob 7s infinite;
            }
            .animation-delay-2000 {
                animation-delay: 2s;
            }
            .animation-delay-4000 {
                animation-delay: 4s;
            }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased overflow-hidden selection:bg-indigo-500 selection:text-white">
        <div class="min-h-screen flex w-full">
            
            <!-- Left Side: Branding & Beautiful Visuals -->
            <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-indigo-900 via-slate-800 to-indigo-950 justify-center items-center relative overflow-hidden">
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
                
                <!-- Glowing Orbs -->
                <div class="absolute top-0 left-[-10%] w-96 h-96 bg-purple-600 rounded-full mix-blend-screen filter blur-[100px] opacity-40 animate-blob"></div>
                <div class="absolute bottom-0 right-[-10%] w-96 h-96 bg-blue-500 rounded-full mix-blend-screen filter blur-[100px] opacity-40 animate-blob animation-delay-2000"></div>
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-indigo-500 rounded-full mix-blend-screen filter blur-[120px] opacity-30 animate-blob animation-delay-4000"></div>

                <!-- Content -->
                <div class="z-10 text-center px-12 flex flex-col items-center">
                    <!-- Glassmorphic Logo Container -->
                    <div class="p-6 bg-white/5 backdrop-blur-xl rounded-3xl border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.3)] mb-8 transform hover:scale-105 transition-transform duration-500">
                        <img src="{{ asset('smec-white.png') }}" class="w-40 object-contain drop-shadow-2xl" alt="SMEC Logo" />
                    </div>
                    
                    <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-blue-200 to-indigo-100 mb-4 tracking-tight leading-tight">
                        Human Resource<br/>Management System
                    </h1>
                    <p class="text-indigo-200/80 text-lg max-w-md font-medium">
                        Streamline your operations with intelligent HR, seamless asset management, and powerful reporting.
                    </p>
                </div>
            </div>

            <!-- Right Side: Form Container -->
            <div class="w-full lg:w-1/2 flex flex-col justify-center items-center bg-[#f8fafc] relative">
                
                <!-- Mobile Logo -->
                <div class="lg:hidden mb-10 text-center">
                    <img src="{{ asset('logo-new.webp') }}" class="w-36 mx-auto drop-shadow-xl" alt="SMEC Logo" />
                </div>
                
                <div class="w-full sm:max-w-md px-10 py-12 bg-white shadow-[0_20px_50px_-12px_rgba(0,0,0,0.1)] sm:rounded-3xl border border-gray-100/50 z-10 transition-all duration-300">
                    <div class="mb-8 text-center">
                        <h2 class="text-3xl font-bold text-slate-800 tracking-tight">Welcome Back</h2>
                        <p class="text-sm text-slate-500 mt-2 font-medium">Please enter your credentials to securely login.</p>
                    </div>
                    
                    {{ $slot }}
                    
                </div>
                
                <!-- Footer for right side -->
                <div class="absolute bottom-6 text-xs text-slate-400 font-medium">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </div>
            </div>

        </div>
    </body>
</html>
