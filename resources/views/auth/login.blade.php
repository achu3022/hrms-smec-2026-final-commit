<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
            <input id="email" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:text-sm px-4 py-3 transition-colors bg-slate-50" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="admin@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-500" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
            <input id="password" class="mt-1 block w-full rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:text-sm px-4 py-3 transition-colors bg-slate-50" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-500" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center mt-4">
            <input id="remember_me" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" name="remember">
            <label for="remember_me" class="ml-2 block text-sm text-slate-600 font-medium">{{ __('Remember me') }}</label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex justify-center items-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-indigo-500/30 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                Sign in to Dashboard
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </button>
        </div>
        <div class="mt-6 text-center border-t border-slate-200 pt-4">
            <a href="{{ route('employee.login') }}" class="text-sm font-medium text-slate-500 hover:text-slate-700">
                &larr; Back to Employee Login
            </a>
        </div>
    </form>
</x-guest-layout>
