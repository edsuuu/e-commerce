<section class="min-h-screen bg-[#f2f3f5] pb-12">
    <div class="bg-white border-b border-zinc-200">
        <div class="mx-auto flex max-w-7xl items-center gap-2 px-3 py-3 text-xs font-bold text-zinc-500 md:px-4">
            <a href="{{ route('home') }}" class="hover:text-[#ff6500]" wire:navigate>Home</a>
            <span>/</span>
            @if($product->category)
                <a href="{{ route('home', ['category' => $product->category_id]) }}" class="hover:text-[#ff6500]" wire:navigate>{{ $product->category->name }}</a>
                <span>/</span>
            @endif
            <span class="line-clamp-1 text-[#002144]">{{ $product->name }}</span>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-3 py-5 md:px-4">
        <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="grid gap-5 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-zinc-100 lg:grid-cols-[430px_1fr] lg:p-6">
                <div class="space-y-4">
                    <div class="flex aspect-square items-center justify-center overflow-hidden rounded-2xl bg-zinc-50 ring-1 ring-zinc-100">
                        <img src="{{ $selectedImage }}" alt="{{ $product->name }}" class="h-full w-full object-contain p-6">
                    </div>

                    @if(count($gallery) > 1)
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($gallery as $index => $image)
                                <button
                                    type="button"
                                    wire:click="selectImage({{ $index }})"
                                    class="flex aspect-square cursor-pointer items-center justify-center overflow-hidden rounded-xl border bg-white transition {{ $selectedImageIndex === $index ? 'border-[#ff6500] ring-2 ring-orange-100' : 'border-zinc-200 hover:border-[#ff6500]' }}"
                                >
                                    <img src="{{ $image['url'] }}" alt="{{ $image['alt'] }}" class="h-full w-full object-contain p-2">
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex min-w-0 flex-col">
                    <div class="flex flex-wrap items-center gap-2">
                        @if($product->is_featured)
                            <span class="rounded-lg bg-[#002144] px-2 py-1 text-[10px] font-black uppercase text-white">Destaque</span>
                        @endif
                        @if($discountPercentage)
                            <span class="rounded-lg bg-[#ff6500] px-2 py-1 text-[10px] font-black uppercase text-white">{{ $discountPercentage }}% OFF</span>
                        @endif
                        <span class="rounded-lg bg-green-50 px-2 py-1 text-[10px] font-black uppercase text-green-700">Em estoque</span>
                    </div>

                    <h1 class="mt-4 text-2xl font-black leading-tight text-[#002144] md:text-3xl">{{ $product->name }}</h1>

                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs font-bold text-zinc-500">
                        <span>SKU: {{ $product->sku }}</span>
                        @if($product->category)
                            <span>Categoria: {{ $product->category->name }}</span>
                        @endif
                    </div>

                    @if($product->short_description)
                        <p class="mt-5 rounded-2xl bg-[#f2f3f5] p-4 text-sm font-medium leading-6 text-zinc-700">
                            {{ $product->short_description }}
                        </p>
                    @endif

                    <div class="mt-6 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl border border-zinc-200 p-4">
                            <p class="text-[10px] font-black uppercase text-zinc-400">Entrega</p>
                            <p class="mt-1 text-sm font-black text-[#002144]">Envio rápido</p>
                        </div>
                        <div class="rounded-2xl border border-zinc-200 p-4">
                            <p class="text-[10px] font-black uppercase text-zinc-400">Garantia</p>
                            <p class="mt-1 text-sm font-black text-[#002144]">Nacional</p>
                        </div>
                        <div class="rounded-2xl border border-zinc-200 p-4">
                            <p class="text-[10px] font-black uppercase text-zinc-400">Estoque</p>
                            <p class="mt-1 text-sm font-black text-[#002144]">{{ $product->stock_quantity }} unidades</p>
                        </div>
                    </div>

                    <div class="mt-auto pt-6">
                        <h2 class="text-sm font-black uppercase text-[#002144]">Descrição do produto</h2>
                        <div class="mt-3 max-w-none text-sm leading-7 text-zinc-700 [&_p]:mb-3 [&_strong]:text-[#002144]">
                            @if($product->description)
                                {!! $product->description !!}
                            @else
                                <p>Produto com pronta entrega, garantia nacional e compra segura pela TECHSHOP.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <aside class="h-fit rounded-2xl bg-white p-5 shadow-sm ring-1 ring-zinc-100 lg:sticky lg:top-36">
                <p class="text-[11px] font-black uppercase text-zinc-400">Preço à vista no PIX</p>

                @if($product->compare_at_price_cents)
                    <p class="mt-3 text-sm font-bold text-zinc-400 line-through">
                        R$ {{ number_format($product->compare_at_price_cents / 100, 2, ',', '.') }}
                    </p>
                @endif

                <p class="mt-1 text-4xl font-black tracking-normal text-[#ff6500]">
                    R$ {{ number_format($product->price_cents / 100, 2, ',', '.') }}
                </p>
                <p class="mt-1 text-xs font-bold text-zinc-500">
                    ou em até 10x de R$ {{ number_format($installmentValueCents / 100, 2, ',', '.') }} sem juros
                </p>

                <div class="mt-5 rounded-2xl bg-[#f2f3f5] p-4">
                    <label for="shipping_zipcode" class="text-[11px] font-black uppercase text-[#002144]">Calcular frete</label>
                    <div class="mt-2 flex gap-2">
                        <input
                            id="shipping_zipcode"
                            type="text"
                            placeholder="Digite seu CEP"
                            class="min-w-0 flex-1 rounded-xl border-zinc-200 bg-white text-sm font-bold text-zinc-900 placeholder:text-zinc-400 focus:border-[#ff6500] focus:ring-[#ff6500]"
                        >
                        <button type="button" class="cursor-pointer rounded-xl bg-[#002144] px-4 text-xs font-black uppercase text-white hover:bg-[#003a70]">
                            OK
                        </button>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    <button
                        type="button"
                        wire:click="buyNow"
                        class="flex w-full cursor-pointer items-center justify-center rounded-xl bg-[#ff6500] px-4 py-3 text-sm font-black uppercase text-white shadow-lg shadow-orange-500/20 transition hover:bg-[#e45b00] active:scale-95"
                    >
                        Comprar agora
                    </button>
                    <button
                        type="button"
                        wire:click="addToCart"
                        class="flex w-full cursor-pointer items-center justify-center rounded-xl border-2 border-[#002144] px-4 py-3 text-sm font-black uppercase text-[#002144] transition hover:bg-[#002144] hover:text-white active:scale-95"
                    >
                        Adicionar ao carrinho
                    </button>
                </div>

                <div class="mt-5 border-t border-zinc-100 pt-5">
                    <h2 class="text-xs font-black uppercase text-[#002144]">Informações técnicas</h2>
                    <dl class="mt-3 space-y-2 text-sm">
                        <div class="flex justify-between gap-4">
                            <dt class="font-medium text-zinc-500">SKU</dt>
                            <dd class="font-bold text-[#002144]">{{ $product->sku }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="font-medium text-zinc-500">Peso</dt>
                            <dd class="font-bold text-[#002144]">{{ $product->weight_kg ? $product->weight_kg.' kg' : 'Nao informado' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt class="font-medium text-zinc-500">Disponibilidade</dt>
                            <dd class="font-bold text-green-700">Pronta entrega</dd>
                        </div>
                    </dl>
                </div>
            </aside>
        </div>

        @if($product->attributes)
            <div class="mt-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-zinc-100">
                <h2 class="text-sm font-black uppercase text-[#002144]">Especificações</h2>
                <dl class="mt-4 grid gap-3 md:grid-cols-2">
                    @foreach($product->attributes as $name => $value)
                        <div class="flex justify-between gap-4 rounded-xl bg-[#f2f3f5] px-4 py-3 text-sm">
                            <dt class="font-bold text-zinc-500">{{ str($name)->headline() }}</dt>
                            <dd class="text-right font-black text-[#002144]">{{ is_scalar($value) ? $value : json_encode($value) }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        @endif

        <section class="mt-5 rounded-2xl bg-white p-5 shadow-sm ring-1 ring-zinc-100">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-[11px] font-black uppercase tracking-widest text-[#ff6500]">Avaliações</p>
                    <h2 class="mt-1 text-2xl font-black text-[#002144]">Opinião de quem comprou</h2>
                    <p class="mt-2 max-w-2xl text-sm font-medium leading-6 text-zinc-600">
                        Veja o resumo das avaliações cadastradas para este produto e os comentários mais recentes dos clientes.
                    </p>
                </div>

                <div class="rounded-2xl bg-[#f2f3f5] px-5 py-4 text-center">
                    <p class="text-4xl font-black text-[#ff6500]">{{ number_format($averageRating, 1, ',', '.') }}</p>
                    <div class="mt-1 flex justify-center gap-0.5 text-lg">
                        @for($star = 1; $star <= 5; $star++)
                            <span class="{{ $star <= round($averageRating) ? 'text-[#ff6500]' : 'text-zinc-300' }}">★</span>
                        @endfor
                    </div>
                    <p class="mt-1 text-xs font-bold text-zinc-500">{{ $reviewCount }} avaliações</p>
                </div>
            </div>

            <div class="mt-6 grid gap-6 lg:grid-cols-[320px_1fr]">
                <div class="space-y-3">
                    @foreach($ratingDistribution as $rating => $count)
                        @php($percentage = $reviewCount > 0 ? round(($count / $reviewCount) * 100) : 0)
                        <div class="grid grid-cols-[48px_1fr_42px] items-center gap-3 text-sm">
                            <span class="font-black text-[#002144]">{{ $rating }} ★</span>
                            <div class="h-2 overflow-hidden rounded-full bg-zinc-100">
                                <div class="h-full rounded-full bg-[#ff6500]" style="width: {{ $percentage }}%"></div>
                            </div>
                            <span class="text-right font-bold text-zinc-500">{{ $count }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="grid gap-3">
                    @forelse($reviews as $review)
                        <article class="rounded-2xl border border-zinc-100 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h3 class="font-black text-[#002144]">{{ $review->title ?? 'Avaliação do produto' }}</h3>
                                    <p class="mt-1 text-xs font-bold text-zinc-500">
                                        {{ $review->customer_name }} · {{ $review->created_at?->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="flex gap-0.5 text-sm">
                                    @for($star = 1; $star <= 5; $star++)
                                        <span class="{{ $star <= $review->rating ? 'text-[#ff6500]' : 'text-zinc-300' }}">★</span>
                                    @endfor
                                </div>
                            </div>

                            @if($review->comment)
                                <p class="mt-3 text-sm leading-6 text-zinc-600">{{ $review->comment }}</p>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-zinc-200 p-6 text-center">
                            <p class="text-sm font-bold text-zinc-500">Este produto ainda não tem avaliações.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        @if($relatedProducts->isNotEmpty())
            <section class="mt-8">
                <div class="mb-4 flex items-center gap-3">
                    <div class="h-10 w-1 rounded-full bg-[#ff6500]"></div>
                    <div>
                        <h2 class="text-xl font-black italic uppercase text-[#002144]">Produtos relacionados</h2>
                        <p class="text-[11px] font-bold uppercase tracking-widest text-zinc-400">Veja outras ofertas da categoria</p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($relatedProducts as $relatedProduct)
                        <a
                            href="{{ route('products.show', $relatedProduct->slug) }}"
                            wire:navigate
                            class="group rounded-2xl bg-white p-4 shadow-sm ring-1 ring-zinc-100 transition hover:-translate-y-1 hover:shadow-xl"
                        >
                            <div class="flex aspect-square items-center justify-center rounded-2xl bg-zinc-50">
                                <img src="{{ $relatedProduct->primary_image_url }}" alt="{{ $relatedProduct->name }}" class="h-full w-full object-contain p-4 transition group-hover:scale-105">
                            </div>
                            <p class="mt-4 line-clamp-2 text-sm font-bold text-[#002144] group-hover:text-[#ff6500]">{{ $relatedProduct->name }}</p>
                            <p class="mt-3 text-xl font-black text-[#ff6500]">R$ {{ number_format($relatedProduct->price_cents / 100, 2, ',', '.') }}</p>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</section>
