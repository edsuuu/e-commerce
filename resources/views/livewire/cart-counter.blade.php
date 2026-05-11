<a href="{{ route('cart.show') }}" class="relative flex h-10 w-10 items-center justify-center rounded-full bg-[#ff6500] text-xl transition hover:scale-105 hover:bg-[#e45b00]" wire:navigate>
    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
    </svg>
    @if($count > 0)
        <span class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-white text-[10px] font-black text-[#002144] shadow-sm">
            {{ $count }}
        </span>
    @endif
</a>
