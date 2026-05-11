<section class="min-h-screen bg-[#f3f4f8] px-4 py-6">
    <div class="mx-auto max-w-6xl">
        <div class="mb-5 flex items-center justify-between rounded bg-white p-4 shadow-sm">
            <div>
                <h1 class="text-2xl font-black text-zinc-950">Meu carrinho</h1>
                <p class="text-sm text-zinc-500">Revise os itens antes de finalizar a compra.</p>
            </div>
            <a href="{{ route('home') }}" class="rounded bg-[#ff6500] px-4 py-2 text-sm font-bold text-white" wire:navigate>Continuar comprando</a>
        </div>

        @if($items === [])
            <div class="rounded bg-white p-10 text-center shadow-sm">
                <h2 class="text-xl font-bold text-zinc-900">Seu carrinho está vazio</h2>
                <p class="mt-2 text-sm text-zinc-500">Escolha alguns produtos para seguir para o checkout.</p>
            </div>
        @else
            <div class="grid gap-5 lg:grid-cols-[1fr_340px]">
                <div class="space-y-3">
                    @foreach($items as $item)
                        <div class="grid gap-4 rounded bg-white p-4 shadow-sm sm:grid-cols-[110px_1fr_auto] sm:items-center">
                            <img src="{{ $item['product']->primary_image_url }}" alt="{{ $item['product']->name }}" class="h-28 w-28 rounded bg-zinc-50 object-contain p-2">
                            <div>
                                <h2 class="font-bold text-zinc-900">{{ $item['product']->name }}</h2>
                                <p class="mt-1 text-sm text-zinc-500">SKU {{ $item['product']->sku }}</p>
                                <button wire:click="remove({{ $item['product']->id }})" class="mt-3 text-sm font-bold text-[#ff6500]">Remover</button>
                            </div>
                            <div class="flex items-center justify-between gap-4 sm:flex-col sm:items-end">
                                <div class="inline-flex items-center rounded border border-zinc-200">
                                    <button wire:click="decrement({{ $item['product']->id }})" class="h-9 w-9 text-lg font-bold">-</button>
                                    <span class="w-10 text-center text-sm font-bold">{{ $item['quantity'] }}</span>
                                    <button wire:click="increment({{ $item['product']->id }})" class="h-9 w-9 text-lg font-bold">+</button>
                                </div>
                                <p class="text-lg font-black text-zinc-950">R$ {{ number_format($item['total'] / 100, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <aside class="h-fit rounded bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-black text-zinc-950">Resumo</h2>
                    <div class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-zinc-500">Subtotal</span>
                            <span class="font-bold text-zinc-900">R$ {{ number_format($subtotal / 100, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-zinc-500">Frete</span>
                            <span class="font-bold text-zinc-900">Calculado depois</span>
                        </div>
                    </div>
                    <div class="mt-5 border-t border-zinc-200 pt-5">
                        <div class="flex justify-between">
                            <span class="font-bold text-zinc-900">Total</span>
                            <span class="text-2xl font-black text-[#ff6500]">R$ {{ number_format($subtotal / 100, 2, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('checkout.show') }}" class="mt-5 block rounded bg-[#ff6500] px-4 py-3 text-center text-sm font-black uppercase text-white" wire:navigate>Fechar pedido</a>
                        <button wire:click="clear" class="mt-3 w-full rounded border border-zinc-200 px-4 py-2 text-sm font-bold text-zinc-700">Limpar carrinho</button>
                    </div>
                </aside>
            </div>
        @endif
    </div>
</section>
