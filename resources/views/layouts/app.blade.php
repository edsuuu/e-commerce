<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>
            {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
        </title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance

        @livewireStyles
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800 {{ request()->routeIs('login', 'register') ? 'overflow-hidden' : '' }}">
        @php($isStorefront = request()->routeIs('home', 'products.*', 'cart.*', 'checkout.*', 'login', 'register'))

        @if($layout === 'sidebar')
            <x-sidebar />
        @elseif($isStorefront)
            @php($storeCategories = \App\Models\Category::query()->active()->whereIn('name', ['Hardware', 'Periféricos', 'PC Gamer', 'Monitores', 'Placas de Vídeo'])->orderBy('sort_order')->get())
            <header class="sticky top-0 z-40 bg-[#002144] text-white shadow-md">
                <div class="mx-auto flex max-w-7xl items-center gap-6 px-3 py-4 md:px-4">
                    <a href="{{ route('home') }}" class="shrink-0" wire:navigate>
                         <span class="text-3xl font-black italic tracking-tighter text-white">TECH<span class="text-[#ff6500]">SHOP</span></span>
                    </a>

                    <form action="{{ route('home') }}" class="flex min-w-0 flex-1 overflow-hidden rounded bg-white text-zinc-900 shadow-inner">
                        <input
                            name="busca"
                            type="search"
                            value="{{ request('busca') }}"
                            placeholder="Busque no TechShop!"
                            class="min-w-0 flex-1 border-0 bg-white px-4 py-3 text-sm font-medium outline-none placeholder:text-zinc-400 focus:ring-0"
                        >
                        <button class="flex items-center justify-center bg-[#ff6500] px-5 transition hover:bg-[#e45b00]" type="submit">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </button>
                    </form>

                    <div class="hidden items-center gap-5 text-xs font-bold lg:flex">
                        <div class="flex items-center gap-2">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-white/20 text-xl text-white/80">👤</div>
                            <div class="flex flex-col leading-tight">
                                @auth
                                    <span class="text-white/60">Olá, {{ auth()->user()->name }}</span>
                                    <a href="{{ route('profile.edit') }}" class="text-sm font-black hover:text-[#ff6500]" wire:navigate>Minha Conta</a>
                                @else
                                    <span class="text-white/60">Entre ou</span>
                                    <a href="{{ route('login') }}" class="text-sm font-black hover:text-[#ff6500]" wire:navigate>Cadastre-se</a>
                                @endauth
                            </div>
                        </div>

                        <div class="flex items-center gap-4 border-l border-white/10 pl-5">
                            <a href="#" class="text-white opacity-80 hover:opacity-100">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </a>
                            <livewire:cart.cart-counter />
                        </div>
                    </div>
                </div>

                <nav class="bg-[#003a70] shadow-sm">
                    <div class="mx-auto flex max-w-7xl items-center gap-6 overflow-x-auto px-3 py-2 text-xs font-black uppercase tracking-tight md:px-4">
                        <div class="flex items-center gap-1 rounded bg-[#ff6500] px-3 py-1.5 text-white">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                            DEPARTAMENTOS
                        </div>
                        <a href="{{ route('home') }}" class="{{ blank(request('category')) ? 'text-[#ff6500]' : '' }} hover:text-[#ff6500] hover:underline" wire:navigate>Ofertas do dia</a>
                        @foreach($storeCategories as $storeCategory)
                            <a
                                href="{{ route('home', ['category' => $storeCategory->id]) }}"
                                class="{{ (int) request('category') === $storeCategory->id ? 'text-[#ff6500]' : '' }} hover:text-[#ff6500]"
                                wire:navigate
                            >
                                {{ $storeCategory->name }}
                            </a>
                        @endforeach
                        <span class="ml-auto hidden items-center gap-1 text-white/60 md:flex">
                             <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                             Enviar para: <span class="font-bold text-white">Digite o CEP</span>
                        </span>
                    </div>
                </nav>
            </header>
        @elseif($layout === 'navbar')
            <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:sidebar.toggle class="lg:hidden mr-2" icon="bars-2" inset="left" />

                <x-app-logo href="{{ route('home') }}" wire:navigate />

                @auth
                    @if(auth()->user()->hasRole('Administrador'))
                        <flux:navbar class="-mb-px max-lg:hidden">
                            <flux:navbar.item icon="folder-git-2" href="/admin">
                                Admin
                            </flux:navbar.item>
                        </flux:navbar>
                    @endif
                @endauth

                <flux:spacer />

                <flux:navbar class="-mb-px">
                    <flux:navbar.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
                        Loja
                    </flux:navbar.item>
                    <flux:navbar.item :href="route('cart.show')" :current="request()->routeIs('cart.*')" wire:navigate>
                        Carrinho
                    </flux:navbar.item>
                </flux:navbar>

                @auth
                    <flux:dropdown position="bottom" align="start">
                        <flux:sidebar.profile
                            :name="auth()->user()->name"
                            :initials="auth()->user()->initials()"
                            icon:trailing="chevrons-up-down"
                            data-test="sidebar-menu-button"
                        />

                        <flux:menu>
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                            <flux:menu.separator />
                            <flux:menu.radio.group>
                                <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                                    {{ __('Settings') }}
                                </flux:menu.item>
                                <form method="POST" action="{{ route('logout') }}" class="w-full">
                                    @csrf
                                    <flux:menu.item
                                        as="button"
                                        type="submit"
                                        icon="arrow-right-start-on-rectangle"
                                        class="w-full cursor-pointer"
                                        data-test="logout-button"
                                    >
                                        {{ __('Log out') }}
                                    </flux:menu.item>
                                </form>
                            </flux:menu.radio.group>
                        </flux:menu>
                    </flux:dropdown>
                @endauth

                @guest
                    <div class="flex items-center gap-4">
                        <flux:navbar.item :href="route('login')" :current="request()->routeIs('login')" wire:navigate>
                            {{ __('Log in') }}
                        </flux:navbar.item>
                        <flux:navbar.item :href="route('register')" :current="request()->routeIs('register')" wire:navigate>
                            {{ __('Register') }}
                        </flux:navbar.item>
                    </div>
                @endguest
            </flux:header>
        @endif

        @if($isStorefront)
            <main>
                {{ $slot }}
            </main>
        @else
            <flux:main>
                {{ $slot }}
            </flux:main>
        @endif

        @livewireScripts
        @fluxScripts
    </body>
</html>
