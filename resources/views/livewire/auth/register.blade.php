<div class="flex flex-col gap-3 sm:gap-4">
    <!-- Session Status -->
    @if (session('status'))
        <div class="text-center text-sm font-medium text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <a
        href="{{ route('auth.google.redirect') }}"
        class="flex w-full cursor-pointer items-center justify-center gap-3 rounded-xl border border-zinc-200 bg-white px-4 py-2.5 text-sm font-black text-[#002144] transition hover:border-[#ff6500] hover:bg-orange-50 sm:py-3"
    >
        <span class="flex h-6 w-6 items-center justify-center rounded-full border border-zinc-200 text-sm font-black text-[#ff6500]">G</span>
        Continuar com Google
    </a>

    <div class="flex items-center gap-3">
        <div class="h-px flex-1 bg-zinc-200"></div>
        <span class="text-[11px] font-bold uppercase text-zinc-400">ou</span>
        <div class="h-px flex-1 bg-zinc-200"></div>
    </div>

    <form wire:submit="register" class="flex flex-col gap-3 sm:gap-4">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <div class="grid gap-3 sm:grid-cols-2">
            <!-- Password -->
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                wire:model="password_confirmation"
                label="Confirmar senha"
                type="password"
                required
                autocomplete="new-password"
                placeholder="Confirmar senha"
                viewable
            />
        </div>

        <div class="flex items-center justify-end">
            <button
                type="submit"
                class="w-full cursor-pointer rounded-xl bg-[#ff6500] px-4 py-3 text-sm font-black text-white transition hover:bg-[#e45b00]"
            >
                {{ __('Create account') }}
            </button>
        </div>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-600 rtl:space-x-reverse dark:text-zinc-400">
        <span>{{ __('Already have an account?') }}</span>
        <a href="{{ route('login') }}" class="font-bold text-[#ff6500] hover:text-[#e45b00]" wire:navigate>
            {{ __('Log in') }}
        </a>
    </div>
</div>
