<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Right Side (Notifications & Settings) -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                <!-- Notifications Dropdown -->
                <div x-data="adminNotifications({{ Auth::id() }}, {{ json_encode(Auth::user()->unreadNotifications->map(fn($n) => ['id' => $n->id, 'message' => $n->data['message'], 'time' => $n->created_at->diffForHumans()])) }})">
                    <x-dropdown align="right" width="80">
                        <x-slot name="trigger">
                            <button class="relative inline-flex items-center p-2 text-gray-500 hover:text-indigo-600 focus:outline-none transition ease-in-out duration-150">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <template x-if="unreadCount > 0">
                                    <span class="absolute top-1 right-1 flex h-2.5 w-2.5">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500 border border-white"></span>
                                    </span>
                                </template>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="max-h-[28rem] overflow-y-auto">
                                <div class="px-4 py-3 border-b border-gray-100 font-semibold text-gray-800 bg-gray-50/80 flex justify-between items-center sticky top-0 backdrop-blur-sm z-10">
                                    <span>Notifications</span>
                                    <template x-if="unreadCount > 0">
                                        <span x-text="unreadCount + ' new'" class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700"></span>
                                    </template>
                                </div>
                                
                                <template x-if="notifications.length === 0">
                                    <div class="px-4 py-8 text-sm text-gray-400 text-center flex flex-col items-center">
                                        <svg class="w-8 h-8 mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                        <p>No new notifications</p>
                                    </div>
                                </template>

                                <template x-for="notification in notifications" :key="notification.id">
                                    <div class="px-4 py-3 border-b border-gray-50 hover:bg-indigo-50/50 transition-colors flex justify-between items-start group">
                                        <div class="text-sm text-gray-600 pr-3">
                                            <span x-text="notification.message" class="text-gray-700 font-medium leading-tight"></span>
                                            <div class="text-[11px] text-gray-400 mt-1 flex items-center gap-1 font-medium">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <span x-text="notification.time"></span>
                                            </div>
                                        </div>
                                        <form method="POST" :action="`/admin/notifications/${notification.id}/mark-as-read`">
                                            @csrf
                                            <button type="submit" class="text-[10px] uppercase font-bold tracking-wider text-indigo-600 opacity-0 group-hover:opacity-100 transition-opacity hover:text-indigo-800 shrink-0 bg-indigo-50 hover:bg-indigo-100 px-2 py-1 rounded">Mark Read</button>
                                        </form>
                                    </div>
                                </template>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<!-- Push Alpine component for notifications -->
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('adminNotifications', (userId, initialNotifications) => ({
            notifications: initialNotifications,
            unreadCount: initialNotifications.length,
            init() {
                if (window.Echo) {
                    window.Echo.private(`App.Models.User.${userId}`)
                        .notification((notification) => {
                            this.notifications.unshift({
                                id: notification.id,
                                message: notification.message,
                                time: 'Just now'
                            });
                            this.unreadCount++;
                        });
                }
            }
        }))
    });
</script>
