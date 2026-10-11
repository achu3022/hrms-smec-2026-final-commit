<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Set New Password - {{ config('app.name', 'HRMS') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f0f4f8; }
        .condition-met { color: #10b981; } /* emerald-500 */
        .condition-unmet { color: #94a3b8; } /* slate-400 */
        .strength-bar { height: 4px; border-radius: 2px; transition: all 0.3s ease; }
    </style>
</head>
<body class="antialiased text-gray-900 min-h-screen flex items-center justify-center bg-slate-50">

    <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-blue-50 z-0"></div>
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-40 z-0"></div>

    <div class="relative z-10 w-full max-w-md lg:max-w-5xl mx-4">
        <div class="bg-white rounded-3xl shadow-2xl flex flex-col lg:flex-row overflow-hidden border border-slate-100">
            
            <!-- Left Side Branding (Hidden on mobile) -->
            <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-indigo-600 to-blue-800 p-12 flex-col justify-between relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-400 opacity-20 rounded-full -ml-20 -mb-20 blur-2xl"></div>
                
                <div class="relative z-10">
                    <img src="{{ asset('logo-new.webp') }}" class="h-12 brightness-0 invert mb-8" alt="SMEC Logo">
                    <h2 class="text-4xl font-bold text-white leading-tight mb-4">Secure your<br>employee account.</h2>
                    <p class="text-indigo-100 text-lg">Set a strong password to access your dashboard, payslips, and leave portal.</p>
                </div>
                
                <div class="relative z-10 text-indigo-200 text-sm">
                    &copy; {{ date('Y') }} SMEC Labs. All rights reserved.
                </div>
            </div>

            <!-- Right Side Form -->
            <div class="w-full lg:w-1/2 p-8 sm:p-12">
                <div class="text-center lg:text-left mb-8">
                    <img src="{{ asset('logo-new.webp') }}" class="h-12 mx-auto lg:hidden mb-6" alt="SMEC Logo">
                    <h2 class="text-2xl font-bold text-slate-800">Set New Password</h2>
                    <p class="text-slate-500 text-sm mt-1">Please create a strong password.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 text-red-600 rounded-xl text-sm border border-red-100 flex items-start">
                        <i class="fa-solid fa-circle-exclamation mt-0.5 mr-2"></i>
                        <span>{{ $errors->first() }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('employee.password.update') }}" class="space-y-5" id="passwordForm">
                    @csrf

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700">New Password</label>
                        <div class="mt-2 relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                            </div>
                            <input id="password" name="password" type="password" required autofocus class="block w-full pl-11 pr-4 py-3 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-slate-50 transition-colors" placeholder="Enter new password">
                        </div>
                    </div>

                    <!-- Password Strength Bar -->
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                        <div id="strengthBar" class="strength-bar w-0 bg-red-500"></div>
                    </div>

                    <!-- Real-time Validations -->
                    <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-100 text-sm mt-2">
                        <p class="font-semibold text-slate-700 mb-2 text-xs uppercase tracking-wider">Password Requirements</p>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 gap-x-4 text-xs">
                            <li id="cond-length" class="condition-unmet flex items-center gap-2">
                                <i class="fa-solid fa-circle-check"></i> At least 6 characters
                            </li>
                            <li id="cond-lower" class="condition-unmet flex items-center gap-2">
                                <i class="fa-solid fa-circle-check"></i> One lowercase
                            </li>
                            <li id="cond-upper" class="condition-unmet flex items-center gap-2">
                                <i class="fa-solid fa-circle-check"></i> One uppercase
                            </li>
                            <li id="cond-number" class="condition-unmet flex items-center gap-2">
                                <i class="fa-solid fa-circle-check"></i> One number
                            </li>
                            <li id="cond-special" class="condition-unmet flex items-center gap-2 sm:col-span-2">
                                <i class="fa-solid fa-circle-check"></i> One special character (!@#$%^&*)
                            </li>
                        </ul>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700">Confirm Password</label>
                        <div class="mt-2 relative rounded-xl shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-solid fa-lock-check"></i>
                            </div>
                            <input id="password_confirmation" name="password_confirmation" type="password" required class="block w-full pl-11 pr-4 py-3 border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-slate-50 transition-colors" placeholder="Confirm new password">
                        </div>
                        <p id="match-text" class="text-xs mt-2 hidden"></p>
                    </div>

                    <button type="submit" id="submitBtn" disabled class="w-full flex justify-center py-3.5 px-4 mt-8 border border-transparent rounded-xl shadow-md text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                        Update Password & Login <i class="fa-solid fa-arrow-right ml-2 mt-0.5"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmInput = document.getElementById('password_confirmation');
            const submitBtn = document.getElementById('submitBtn');
            const strengthBar = document.getElementById('strengthBar');
            const matchText = document.getElementById('match-text');

            const conditions = {
                length: document.getElementById('cond-length'),
                lower: document.getElementById('cond-lower'),
                upper: document.getElementById('cond-upper'),
                number: document.getElementById('cond-number'),
                special: document.getElementById('cond-special')
            };

            function updateCondition(element, isMet) {
                if (isMet) {
                    element.classList.remove('condition-unmet');
                    element.classList.add('condition-met');
                } else {
                    element.classList.remove('condition-met');
                    element.classList.add('condition-unmet');
                }
            }

            function validatePassword() {
                const p = passwordInput.value;
                const c = confirmInput.value;
                
                let score = 0;
                
                // Conditions
                const hasLength = p.length >= 6;
                const hasLower = /[a-z]/.test(p);
                const hasUpper = /[A-Z]/.test(p);
                const hasNumber = /\d/.test(p);
                const hasSpecial = /[\W_]/.test(p); // Non-alphanumeric

                updateCondition(conditions.length, hasLength);
                updateCondition(conditions.lower, hasLower);
                updateCondition(conditions.upper, hasUpper);
                updateCondition(conditions.number, hasNumber);
                updateCondition(conditions.special, hasSpecial);

                if (hasLength) score += 20;
                if (hasLower) score += 20;
                if (hasUpper) score += 20;
                if (hasNumber) score += 20;
                if (hasSpecial) score += 20;

                strengthBar.style.width = score + '%';
                
                if (score < 40) {
                    strengthBar.className = 'strength-bar bg-red-500';
                } else if (score < 80) {
                    strengthBar.className = 'strength-bar bg-yellow-400';
                } else {
                    strengthBar.className = 'strength-bar bg-emerald-500';
                }

                const allValid = hasLength && hasLower && hasUpper && hasNumber && hasSpecial;
                const matches = p === c && p !== '';

                if (c.length > 0) {
                    matchText.classList.remove('hidden');
                    if (matches) {
                        matchText.innerHTML = '<i class="fa-solid fa-check-circle mr-1"></i> Passwords match';
                        matchText.className = "text-xs mt-2 text-emerald-500 font-medium flex items-center";
                    } else {
                        matchText.innerHTML = '<i class="fa-solid fa-times-circle mr-1"></i> Passwords do not match';
                        matchText.className = "text-xs mt-2 text-red-500 font-medium flex items-center";
                    }
                } else {
                    matchText.classList.add('hidden');
                }

                submitBtn.disabled = !(allValid && matches);
            }

            passwordInput.addEventListener('input', validatePassword);
            confirmInput.addEventListener('input', validatePassword);
        });
    </script>
</body>
</html>
