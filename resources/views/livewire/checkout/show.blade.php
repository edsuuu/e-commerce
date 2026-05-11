<section class="min-h-screen bg-[#f3f4f8] px-4 py-6">
    <div class="mx-auto max-w-6xl">
        @if($completedOrderNumber)
            <div class="rounded bg-white p-10 text-center shadow-sm">
                <p class="text-sm font-bold uppercase text-[#ff6500]">Pedido recebido</p>
                <h1 class="mt-2 text-3xl font-black text-zinc-950">{{ $completedOrderNumber }}</h1>
                <p class="mt-3 text-zinc-500">Seu pedido foi criado e já aparece no painel administrativo.</p>
                <a href="{{ route('home') }}" class="mt-6 inline-flex rounded bg-[#ff6500] px-5 py-3 text-sm font-black uppercase text-white" wire:navigate>Voltar para a loja</a>
            </div>
        @else
            <div class="mb-5 rounded bg-white p-4 shadow-sm">
                <h1 class="text-2xl font-black text-zinc-950">Finalizar compra</h1>
                <p class="text-sm text-zinc-500">Informe os dados de entrega para criar o pedido.</p>
            </div>

            <form wire:submit="placeOrder" class="grid gap-5 lg:grid-cols-[1fr_340px]">
                <div class="space-y-5">
                    @error('cart')
                        <div class="rounded bg-red-50 p-4 text-sm font-bold text-red-700">{{ $message }}</div>
                    @enderror

                    <div class="rounded bg-white p-5 shadow-sm">
                        <h2 class="font-black text-zinc-950">Contato</h2>
                        <div class="mt-4 grid gap-4 sm:grid-cols-2">
                            <flux:input wire:model="customer_name" label="Nome completo" required />
                            <flux:input wire:model="customer_email" label="E-mail" type="email" required />
                            <flux:input wire:model="customer_phone" label="Telefone" />
                        </div>
                    </div>

                    <div class="rounded bg-white p-5 shadow-sm">
                        <h2 class="font-black text-zinc-950">Entrega</h2>
                        <div class="mt-4 grid gap-4 sm:grid-cols-6">
                            <flux:input wire:model="shipping_zipcode" label="CEP" class="sm:col-span-2" required />
                            <flux:input wire:model="shipping_address" label="Endereço" class="sm:col-span-4" required />
                            <flux:input wire:model="shipping_number" label="Número" class="sm:col-span-2" />
                            <flux:input wire:model="shipping_complement" label="Complemento" class="sm:col-span-4" />
                            <flux:input wire:model="shipping_district" label="Bairro" class="sm:col-span-2" />
                            <flux:input wire:model="shipping_city" label="Cidade" class="sm:col-span-3" required />
                            <flux:input wire:model="shipping_state" label="UF" maxlength="2" class="sm:col-span-1" required />
                        </div>
                    </div>
                </div>

                <aside class="h-fit rounded bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-black text-zinc-950">Pedido</h2>
                    <div class="mt-4 space-y-4">
                        @foreach($items as $item)
                            <div class="flex gap-3">
                                <img src="{{ $item['product']->primary_image_url }}" alt="{{ $item['product']->name }}" class="h-16 w-16 rounded bg-zinc-50 object-contain p-1">
                                <div class="min-w-0 flex-1">
                                    <p class="line-clamp-2 text-sm font-bold text-zinc-900">{{ $item['product']->name }}</p>
                                    <p class="text-xs text-zinc-500">{{ $item['quantity'] }} unidade(s)</p>
                                </div>
                                <p class="text-sm font-bold text-zinc-900">R$ {{ number_format($item['total'] / 100, 2, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-5 border-t border-zinc-200 pt-5">
                        <div class="flex justify-between">
                            <span class="font-bold text-zinc-900">Total</span>
                            <span class="text-2xl font-black text-[#ff6500]">R$ {{ number_format($subtotal / 100, 2, ',', '.') }}</span>
                        </div>
                        <flux:button type="submit" variant="primary" class="mt-5 w-full bg-[#ff6500]">
                            Confirmar pedido
                        </flux:button>
                    </div>
                </aside>
            </form>
        @endif
    </div>
</section>
