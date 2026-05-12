<x-layout :title="__('Register')">
    <div class="overflow-hidden bg-[#f2f3f5] px-3 py-3 sm:px-4" style="height: calc(100dvh - 124px);">
        <div class="mx-auto h-full max-w-6xl overflow-hidden rounded-3xl bg-white shadow-xl ring-1 ring-zinc-100 lg:flex">
            <section class="hidden min-h-0 bg-[#002144] p-8 text-white lg:flex lg:w-3/5 lg:flex-col lg:justify-between">
                <a href="{{ route('home') }}" class="w-fit" wire:navigate>
                    <span class="text-4xl font-black italic tracking-tighter text-white">TECH<span class="text-[#ff6500]">SHOP</span></span>
                </a>

                <div class="space-y-6">
                    <div class="inline-flex h-24 w-24 items-center justify-center rounded-[24px] bg-white/10 ring-1 ring-white/15">
                        <img src="/favicon.svg" alt="TechShop" class="h-14 w-14">
                    </div>

                    <div class="max-w-md">
                        <p class="text-xs font-black uppercase tracking-widest text-[#ff6500]">Primeira compra?</p>
                        <h1 class="mt-3 text-5xl font-black leading-none tracking-normal">Crie sua conta</h1>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div class="h-20 rounded-2xl bg-white/10 ring-1 ring-white/10"></div>
                    <div class="h-20 rounded-2xl bg-[#ff6500]"></div>
                    <div class="h-20 rounded-2xl bg-white/10 ring-1 ring-white/10"></div>
                </div>
            </section>

            <section class="flex min-h-0 items-center justify-center p-4 sm:p-6 lg:w-2/5">
                <div class="w-full max-w-sm">
                    <div class="mb-3 text-center lg:text-left">
                        <p class="text-xs font-black uppercase tracking-widest text-[#ff6500]">TECHSHOP</p>
                        <h1 class="mt-1 text-2xl font-black text-[#002144]">Crie sua conta</h1>
                    </div>

                    <livewire:auth.register />
                </div>
            </section>
        </div>
    </div>
</x-layout>
