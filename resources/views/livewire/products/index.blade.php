<section class="min-h-screen bg-[#f2f3f5] pb-12">
    <div class="bg-[#002144]">
        <div class="mx-auto max-w-7xl px-3 py-4 md:px-4">
             <div class="grid gap-4 md:grid-cols-2">
                 <div class="relative overflow-hidden rounded-lg bg-gradient-to-br from-[#003a70] to-[#002144] p-8 text-white shadow-lg">
                     <div class="relative z-10">
                         <span class="inline-block rounded bg-[#ff6500] px-2 py-1 text-[10px] font-black uppercase tracking-wider">Mega Oferta</span>
                         <h1 class="mt-4 text-3xl font-black italic md:text-5xl">PRODUTOS <span class="text-[#ff6500]">GAMER</span></h1>
                         <p class="mt-2 text-lg font-medium text-white/80">O setup dos seus sonhos com até 40% OFF</p>
                         <button class="mt-6 cursor-pointer rounded-xl bg-[#ff6500] px-8 py-3 text-sm font-black uppercase transition hover:scale-105 hover:bg-[#e45b00]">Aproveitar agora</button>
                     </div>
                     <div class="absolute -bottom-10 -right-10 h-64 w-64 opacity-20 grayscale filter">🚀</div>
                 </div>
                 <div class="hidden grid-cols-2 gap-4 md:grid">
                     <div class="rounded-lg bg-white p-6 shadow-sm">
                         <span class="text-xs font-black uppercase text-[#ff6500]">Monte seu PC</span>
                         <p class="mt-2 text-sm font-bold text-[#002144]">As melhores peças para sua máquina.</p>
                         <div class="mt-4 text-4xl">🖥️</div>
                     </div>
                     <div class="rounded-lg bg-[#ff6500] p-6 shadow-sm text-white">
                         <span class="text-xs font-black uppercase opacity-80">Cupons Ativos</span>
                         <p class="mt-2 text-sm font-bold">Use TECH10 para 10% de desconto extra.</p>
                         <div class="mt-4 text-4xl">🎟️</div>
                     </div>
                 </div>
             </div>
        </div>
    </div>

    <div class="mx-auto grid max-w-7xl gap-4 px-3 py-6 md:px-4 lg:grid-cols-[240px_1fr]">
        <aside class="space-y-4">
            <div class="rounded-lg bg-white p-5 shadow-sm">
                <h2 class="flex items-center gap-2 text-xs font-black uppercase text-[#002144]">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Departamentos
                </h2>
                <div class="mt-4 space-y-2">
                    <button wire:click="$set('category', null)" class="flex w-full cursor-pointer items-center justify-between rounded-xl px-3 py-2 text-left text-[13px] transition {{ $category === null ? 'bg-[#ff6500] font-bold text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-50' }}">
                        <span>Todos os produtos</span>
                        @if($category === null) <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg> @endif
                    </button>
                    @foreach($categories as $item)
                        <button wire:click="$set('category', {{ $item->id }})" class="flex w-full cursor-pointer items-center justify-between rounded-xl px-3 py-2 text-left text-[13px] transition {{ $category === $item->id ? 'bg-[#ff6500] font-bold text-white shadow-md' : 'text-zinc-600 hover:bg-zinc-50' }}">
                            <span>{{ $item->name }}</span>
                            @if($category === $item->id) <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"/></svg> @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="rounded-lg bg-[#002144] p-5 text-white shadow-md">
                <p class="text-xs font-black uppercase text-[#ff6500]">Anúncio</p>
                <p class="mt-2 text-sm font-bold">Baixe nosso App e ganhe frete grátis!</p>
                <button class="mt-4 w-full cursor-pointer rounded-xl border border-white/20 py-2 text-[11px] font-black uppercase hover:bg-white/10">Saber mais</button>
            </div>
        </aside>

        <main>
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-1 bg-[#ff6500] rounded-full"></div>
                    <div>
                        <h2 class="text-xl font-black italic uppercase text-[#002144]">Vitrine de <span class="text-[#ff6500]">Ofertas</span></h2>
                        <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest">Encontramos {{ $products->total() }} itens</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="text-[11px] font-bold text-zinc-400 uppercase">Ordenar por:</span>
                    <select wire:model.live="sort" class="cursor-pointer rounded-xl border-zinc-200 bg-white px-3 py-1.5 text-xs font-bold text-[#002144] shadow-sm focus:border-[#ff6500] focus:ring-0">
                        <option value="featured">Mais relevantes</option>
                        <option value="newest">Novidades</option>
                        <option value="price_asc">Menor preço</option>
                        <option value="price_desc">Maior preço</option>
                    </select>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse($products as $product)
                    <article class="group relative flex cursor-pointer flex-col rounded-2xl bg-white p-4 shadow-sm ring-1 ring-zinc-100 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                        <div class="absolute left-4 top-4 z-10 flex flex-col gap-1">
                             <span class="rounded-lg bg-[#ff6500] px-1.5 py-0.5 text-[9px] font-black text-white">OFERTA</span>
                             @if($product->is_featured)
                                <span class="rounded-lg bg-[#002144] px-1.5 py-0.5 text-[9px] font-black text-white">DESTAQUE</span>
                             @endif
                        </div>

                        <a href="{{ route('products.show', $product->slug) }}" wire:navigate class="relative flex aspect-square items-center justify-center overflow-hidden rounded-2xl bg-zinc-50 transition group-hover:bg-white">
                            <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}" class="h-full w-full object-contain p-4 transition-transform duration-500 group-hover:scale-110">
                        </a>

                        <div class="mt-4 flex flex-1 flex-col">
                            <a href="{{ route('products.show', $product->slug) }}" wire:navigate class="block">
                                <p class="text-[10px] font-black uppercase text-zinc-400">{{ $product->category?->name ?? 'Produto' }}</p>
                                <h3 class="mt-1 line-clamp-2 h-10 text-[13px] font-bold leading-5 text-[#002144] group-hover:text-[#ff6500]">{{ $product->name }}</h3>
                            </a>

                            <a href="{{ route('products.show', $product->slug) }}" wire:navigate class="mt-4 block">
                                @if($product->compare_at_price_cents)
                                    <p class="text-[11px] font-medium text-zinc-400 line-through">
                                        R$ {{ number_format($product->compare_at_price_cents / 100, 2, ',', '.') }}
                                    </p>
                                @endif
                                <div class="flex items-baseline gap-1">
                                    <p class="text-2xl font-black text-[#ff6500]">R$ {{ number_format($product->price_cents / 100, 2, ',', '.') }}</p>
                                </div>
                                <p class="text-[10px] font-bold text-zinc-500 italic">À vista no PIX</p>
                            </a>

                            <div class="mt-6 flex flex-col gap-2">
                                <button
                                    wire:click="buyNow({{ $product->id }})"
                                    class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl bg-[#ff6500] py-2.5 text-xs font-black uppercase text-white shadow-lg shadow-orange-500/20 transition-all hover:bg-[#e45b00] active:scale-95"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                    COMPRAR
                                </button>
                                <button
                                    wire:click="addToCart({{ $product->id }})"
                                    class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-xl border-2 border-[#002144] py-2 text-xs font-black uppercase text-[#002144] transition hover:bg-[#002144] hover:text-white active:scale-95"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    CARRINHO
                                </button>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="flex flex-col items-center justify-center rounded-xl bg-white py-20 shadow-sm sm:col-span-2 lg:col-span-3">
                        <div class="text-6xl mb-4 opacity-20">🔎</div>
                        <p class="text-sm font-bold text-zinc-400">Nenhum produto encontrado na sua busca.</p>
                        <button wire:click="$set('search', '')" class="mt-4 cursor-pointer text-xs font-black uppercase text-[#ff6500] hover:underline">Limpar filtros</button>
                    </div>
                @endforelse
            </div>

            <div class="mt-10">
                {{ $products->links() }}
            </div>
        </main>
    </div>
</section>
