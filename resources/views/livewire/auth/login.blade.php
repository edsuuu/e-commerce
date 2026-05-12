<div class="flex flex-col gap-4">
    <!-- Session Status -->
    @if (session('status'))
        <div class="text-center text-sm font-medium text-green-600">
            {{ session('status') }}
        </div>
    @endif

    <a
        href="{{ route('auth.google.redirect') }}"
        class="flex w-full cursor-pointer items-center justify-center gap-3 rounded-xl border border-zinc-200 bg-white px-4 py-3 text-sm font-black text-[#002144] transition hover:border-[#ff6500] hover:bg-orange-50"
    >
        <span class="flex h-6 w-6 items-center justify-center rounded-full border border-zinc-200 text-sm font-black text-[#ff6500]">G</span>
        Continuar com Google
    </a>

    <div class="flex items-center gap-3">
        <div class="h-px flex-1 bg-zinc-200"></div>
        <span class="text-[11px] font-bold uppercase text-zinc-400">ou</span>
        <div class="h-px flex-1 bg-zinc-200"></div>
    </div>

    <form wire:submit="login" class="flex flex-col gap-4">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autofocus
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Password -->
        <div class="relative">
            <flux:input
                wire:model="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="current-password"
                :placeholder="__('Password')"
                viewable
            />

            @if (Route::has('password.request'))
                <flux:link class="absolute top-0 text-sm end-0" :href="route('password.request')" wire:navigate>
                    {{ __('Forgot your password?') }}
                </flux:link>
            @endif
        </div>

        <!-- Remember Me -->
        <label class="inline-flex w-fit cursor-pointer items-center gap-2 text-sm font-medium text-zinc-600">
            <input
                wire:model="remember"
                type="checkbox"
                class="h-4 w-4 cursor-pointer rounded border-zinc-300 text-[#ff6500] focus:ring-[#ff6500]"
            >
            <span>{{ __('Remember me') }}</span>
        </label>

        <div class="flex items-center justify-end">
            <button
                type="submit"
                class="w-full cursor-pointer rounded-xl bg-[#ff6500] px-4 py-3 text-sm font-black text-white transition hover:bg-[#e45b00]"
                data-test="login-button"
            >
                {{ __('Log in') }}
            </button>
        </div>
    </form>

    @if (Route::has('register'))
        <div class="space-x-1 text-center text-sm text-zinc-600 rtl:space-x-reverse dark:text-zinc-400">
            <span>{{ __('Don\'t have an account?') }}</span>
            <a href="{{ route('register') }}" class="font-bold text-[#ff6500] hover:text-[#e45b00]" wire:navigate>
                {{ __('Sign up') }}
            </a>
        </div>
    @endif
</div>
