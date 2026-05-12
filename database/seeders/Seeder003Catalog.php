<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Category;
use App\Models\File as MediaFile;
use App\Models\Product;
use App\Models\ProductFile;
use App\Models\ProductReview;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

final class Seeder003Catalog extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = collect($this->categories())->map(fn (array $category, int $index): Category => Category::query()->updateOrCreate(
            ['slug' => Str::slug($category['name'])],
            [
                ...$category,
                'is_active' => true,
                'sort_order' => $index + 1,
            ],
        ));

        foreach ($this->products() as $index => $item) {
            $category = $categories->firstWhere('name', $item['category']);

            $product = Product::query()->updateOrCreate(
                ['sku' => $item['sku']],
                [
                    'category_id' => $category?->id,
                    'name' => $item['name'],
                    'slug' => Str::slug($item['name']),
                    'short_description' => $item['short_description'],
                    'description' => $this->descriptionFor($item),
                    'price_cents' => $item['price_cents'],
                    'compare_at_price_cents' => $item['compare_at_price_cents'] ?? null,
                    'stock_quantity' => $item['stock_quantity'],
                    'low_stock_threshold' => 5,
                    'is_active' => true,
                    'is_featured' => $index < 10,
                    'weight_kg' => $item['weight_kg'],
                    'attributes' => $item['attributes'],
                    'published_at' => now(),
                ],
            );

            $this->syncProductImage($product);
            $this->syncReviews($product, $index);
        }
    }

    /**
     * @return array<int, array{name: string, description: string}>
     */
    private function categories(): array
    {
        return [
            ['name' => 'Hardware', 'description' => 'Peças para montar ou atualizar seu computador.'],
            ['name' => 'Periféricos', 'description' => 'Teclados, mouses, headsets e acessórios.'],
            ['name' => 'PC Gamer', 'description' => 'Máquinas completas para jogar e trabalhar com performance.'],
            ['name' => 'Monitores', 'description' => 'Telas gamer e profissionais.'],
            ['name' => 'Placas de Vídeo', 'description' => 'GPUs NVIDIA e Radeon para alta performance.'],
            ['name' => 'Processadores', 'description' => 'CPUs Intel e AMD para todo tipo de setup.'],
            ['name' => 'Placas-Mãe', 'description' => 'Bases confiáveis para plataformas modernas.'],
            ['name' => 'Memória RAM', 'description' => 'Kits DDR4 e DDR5 para performance estável.'],
            ['name' => 'Armazenamento', 'description' => 'SSDs NVMe, SATA e soluções de alto desempenho.'],
            ['name' => 'Notebooks', 'description' => 'Notebooks gamer e produtivos.'],
            ['name' => 'Cadeiras Gamer', 'description' => 'Ergonomia para longas sessões no setup.'],
        ];
    }

    /**
     * @return array<int, array{
     *     category: string,
     *     name: string,
     *     sku: string,
     *     price_cents: int,
     *     compare_at_price_cents: int|null,
     *     stock_quantity: int,
     *     weight_kg: string,
     *     accent: string,
     *     short_description: string,
     *     attributes: array<string, string>
     * }>
     */
    private function products(): array
    {
        return [
            ['category' => 'Placas de Vídeo', 'name' => 'Placa de Vídeo MSI GeForce RTX 5070 12G Ventus 2X OC, 12GB GDDR7', 'sku' => 'GPU-MSI-RTX5070-12V2C', 'price_cents' => 429990, 'compare_at_price_cents' => 479990, 'stock_quantity' => 12, 'weight_kg' => '1.250', 'accent' => '#ff6500', 'short_description' => 'GPU de nova geração para jogar em QHD com Ray Tracing, DLSS e excelente eficiência térmica.', 'attributes' => ['GPU' => 'GeForce RTX 5070', 'Memória' => '12GB GDDR7', 'Interface' => 'PCI Express 5.0', 'Indicação' => 'QHD/4K competitivo']],
            ['category' => 'Placas de Vídeo', 'name' => 'Placa de Vídeo ASUS TUF Gaming GeForce RTX 5070 OC, 12GB GDDR7', 'sku' => 'GPU-ASUS-TUF-RTX5070-O12G', 'price_cents' => 469990, 'compare_at_price_cents' => 529990, 'stock_quantity' => 8, 'weight_kg' => '1.380', 'accent' => '#f59e0b', 'short_description' => 'Construção robusta TUF, resfriamento reforçado e desempenho premium para setups exigentes.', 'attributes' => ['GPU' => 'GeForce RTX 5070', 'Memória' => '12GB GDDR7', 'Sistema de fans' => 'Axial-tech', 'Perfil' => 'OC Edition']],
            ['category' => 'Placas de Vídeo', 'name' => 'Placa de Vídeo Gigabyte GeForce RTX 5060 Ti Gaming OC, 16GB GDDR7', 'sku' => 'GPU-GB-RTX5060TI-16G', 'price_cents' => 339990, 'compare_at_price_cents' => 379990, 'stock_quantity' => 16, 'weight_kg' => '1.120', 'accent' => '#22c55e', 'short_description' => 'Equilíbrio entre performance e custo para jogar em Full HD alto e QHD com folga.', 'attributes' => ['GPU' => 'GeForce RTX 5060 Ti', 'Memória' => '16GB GDDR7', 'Cooler' => 'Triple fan', 'Indicação' => 'Full HD/QHD']],
            ['category' => 'Placas de Vídeo', 'name' => 'Placa de Vídeo Radeon RX 7800 XT Challenger, 16GB GDDR6', 'sku' => 'GPU-RX7800XT-16G', 'price_cents' => 359990, 'compare_at_price_cents' => 409990, 'stock_quantity' => 11, 'weight_kg' => '1.180', 'accent' => '#ef4444', 'short_description' => 'Alta memória de vídeo e ótimo desempenho raster para games em QHD.', 'attributes' => ['GPU' => 'Radeon RX 7800 XT', 'Memória' => '16GB GDDR6', 'Barramento' => '256-bit', 'Indicação' => 'QHD Ultra']],
            ['category' => 'Processadores', 'name' => 'Processador AMD Ryzen 7 9700X, 8-Core, 16-Threads, AM5', 'sku' => 'CPU-AMD-R79700X', 'price_cents' => 219990, 'compare_at_price_cents' => 249990, 'stock_quantity' => 18, 'weight_kg' => '0.120', 'accent' => '#ff6500', 'short_description' => 'CPU AM5 eficiente para jogos, edição e multitarefa pesada.', 'attributes' => ['Socket' => 'AM5', 'Núcleos' => '8', 'Threads' => '16', 'Arquitetura' => 'Zen 5']],
            ['category' => 'Processadores', 'name' => 'Processador AMD Ryzen 7 7800X3D, 8-Core, 16-Threads, AM5', 'sku' => 'CPU-AMD-R77800X3D', 'price_cents' => 289990, 'compare_at_price_cents' => 329990, 'stock_quantity' => 14, 'weight_kg' => '0.120', 'accent' => '#ef4444', 'short_description' => 'Processador gamer com 3D V-Cache para taxas de quadros muito altas.', 'attributes' => ['Socket' => 'AM5', 'Cache' => '3D V-Cache', 'Núcleos' => '8', 'Uso' => 'Gaming competitivo']],
            ['category' => 'Processadores', 'name' => 'Processador Intel Core i7-14700K, 20-Core, 28-Threads, LGA1700', 'sku' => 'CPU-INTEL-I714700K', 'price_cents' => 269990, 'compare_at_price_cents' => 309990, 'stock_quantity' => 10, 'weight_kg' => '0.130', 'accent' => '#2563eb', 'short_description' => 'Alto desempenho híbrido para jogos, streaming e criação de conteúdo.', 'attributes' => ['Socket' => 'LGA1700', 'Núcleos' => '20', 'Threads' => '28', 'Perfil' => 'Unlocked']],
            ['category' => 'Placas-Mãe', 'name' => 'Placa-Mãe MSI B650M Gaming Plus WiFi, AM5, DDR5, mATX', 'sku' => 'MB-MSI-B650M-GPWIFI', 'price_cents' => 119990, 'compare_at_price_cents' => 139990, 'stock_quantity' => 20, 'weight_kg' => '0.900', 'accent' => '#7c3aed', 'short_description' => 'Base AM5 moderna com DDR5, Wi-Fi integrado e bons recursos de expansão.', 'attributes' => ['Socket' => 'AM5', 'Memória' => 'DDR5', 'Rede' => 'Wi-Fi', 'Formato' => 'mATX']],
            ['category' => 'Placas-Mãe', 'name' => 'Placa-Mãe ASUS TUF Gaming B760M-Plus WiFi, LGA1700, DDR5', 'sku' => 'MB-ASUS-B760M-TUFWIFI', 'price_cents' => 134990, 'compare_at_price_cents' => 159990, 'stock_quantity' => 15, 'weight_kg' => '0.960', 'accent' => '#f97316', 'short_description' => 'Placa-mãe Intel com construção TUF, conectividade atual e visual sóbrio.', 'attributes' => ['Socket' => 'LGA1700', 'Memória' => 'DDR5', 'Rede' => 'Wi-Fi', 'Chipset' => 'B760']],
            ['category' => 'Memória RAM', 'name' => 'Memória Kingston Fury Beast RGB, 32GB (2x16GB), DDR5 6000MHz', 'sku' => 'RAM-KF-BEAST-32DDR5-6000', 'price_cents' => 79990, 'compare_at_price_cents' => 89990, 'stock_quantity' => 28, 'weight_kg' => '0.090', 'accent' => '#a855f7', 'short_description' => 'Kit DDR5 rápido com RGB para setups gamer e criativos.', 'attributes' => ['Capacidade' => '32GB', 'Frequência' => '6000MHz', 'Tipo' => 'DDR5', 'Formato' => '2x16GB']],
            ['category' => 'Memória RAM', 'name' => 'Memória Corsair Vengeance RGB, 32GB (2x16GB), DDR5 5600MHz', 'sku' => 'RAM-CORSAIR-VEN-32DDR5', 'price_cents' => 72990, 'compare_at_price_cents' => 84990, 'stock_quantity' => 22, 'weight_kg' => '0.095', 'accent' => '#06b6d4', 'short_description' => 'Memória DDR5 estável, com iluminação RGB e perfil para performance.', 'attributes' => ['Capacidade' => '32GB', 'Frequência' => '5600MHz', 'Tipo' => 'DDR5', 'Iluminação' => 'RGB']],
            ['category' => 'Armazenamento', 'name' => 'SSD Kingston NV3, 1TB, M.2 2280, PCIe 4.0 NVMe', 'sku' => 'SSD-KINGSTON-NV3-1TB', 'price_cents' => 42990, 'compare_at_price_cents' => 49990, 'stock_quantity' => 45, 'weight_kg' => '0.020', 'accent' => '#0ea5e9', 'short_description' => 'SSD NVMe PCIe 4.0 com leitura veloz para jogos, sistema e arquivos grandes.', 'attributes' => ['Capacidade' => '1TB', 'Interface' => 'PCIe 4.0', 'Formato' => 'M.2 2280', 'Leitura' => 'Até 6000MB/s']],
            ['category' => 'Armazenamento', 'name' => 'SSD WD Black SN850X, 2TB, M.2 2280, PCIe 4.0 NVMe', 'sku' => 'SSD-WD-SN850X-2TB', 'price_cents' => 109990, 'compare_at_price_cents' => 129990, 'stock_quantity' => 17, 'weight_kg' => '0.025', 'accent' => '#111827', 'short_description' => 'Armazenamento premium para games grandes e carregamentos extremamente rápidos.', 'attributes' => ['Capacidade' => '2TB', 'Interface' => 'PCIe 4.0', 'Formato' => 'M.2 2280', 'Perfil' => 'Gaming']],
            ['category' => 'Hardware', 'name' => 'Fonte Corsair RM850e, 850W, 80 Plus Gold, Modular', 'sku' => 'PSU-CORSAIR-RM850E', 'price_cents' => 74990, 'compare_at_price_cents' => 89990, 'stock_quantity' => 19, 'weight_kg' => '1.900', 'accent' => '#f59e0b', 'short_description' => 'Fonte modular eficiente para placas de vídeo modernas e upgrades futuros.', 'attributes' => ['Potência' => '850W', 'Certificação' => '80 Plus Gold', 'Cabeamento' => 'Modular', 'Padrão' => 'ATX']],
            ['category' => 'Hardware', 'name' => 'Water Cooler Rise Mode Aura Ice, 360mm, ARGB, Intel/AMD', 'sku' => 'COOLER-RM-AURA-360', 'price_cents' => 49990, 'compare_at_price_cents' => 59990, 'stock_quantity' => 24, 'weight_kg' => '1.600', 'accent' => '#38bdf8', 'short_description' => 'Refrigeração líquida de 360mm com ARGB para CPUs de alto desempenho.', 'attributes' => ['Radiador' => '360mm', 'Iluminação' => 'ARGB', 'Compatibilidade' => 'Intel/AMD', 'Uso' => 'Alta performance']],
            ['category' => 'Hardware', 'name' => 'Gabinete Gamer Montech Air 903 Max, Mid Tower, Mesh, 4 Fans', 'sku' => 'CASE-MONTECH-AIR903MAX', 'price_cents' => 39990, 'compare_at_price_cents' => 46990, 'stock_quantity' => 21, 'weight_kg' => '7.200', 'accent' => '#64748b', 'short_description' => 'Gabinete com fluxo de ar forte, frontal mesh e espaço para placas grandes.', 'attributes' => ['Formato' => 'Mid Tower', 'Fans' => '4 inclusas', 'Frontal' => 'Mesh', 'Compatibilidade' => 'ATX']],
            ['category' => 'Monitores', 'name' => 'Monitor Gamer MSI MAG 275QF, 27", QHD, 180Hz, 0.5ms, IPS', 'sku' => 'MON-MSI-MAG275QF', 'price_cents' => 169990, 'compare_at_price_cents' => 219990, 'stock_quantity' => 13, 'weight_kg' => '4.200', 'accent' => '#ef4444', 'short_description' => 'Monitor QHD IPS com 180Hz para imagens fluidas e cores vibrantes.', 'attributes' => ['Tela' => '27 polegadas', 'Resolução' => 'QHD', 'Taxa' => '180Hz', 'Painel' => 'IPS']],
            ['category' => 'Monitores', 'name' => 'Monitor Gamer Rise Mode Prime, 27", QHD, 180Hz, 1ms, IPS', 'sku' => 'MON-RM-27F1802K', 'price_cents' => 107990, 'compare_at_price_cents' => 170455, 'stock_quantity' => 30, 'weight_kg' => '4.600', 'accent' => '#ff6500', 'short_description' => 'Tela 2K de alta atualização para setup gamer com excelente custo-benefício.', 'attributes' => ['Tela' => '27 polegadas', 'Resolução' => 'QHD', 'Taxa' => '180Hz', 'Resposta' => '1ms']],
            ['category' => 'Monitores', 'name' => 'Monitor Gamer Gigabyte GS27QA, 27", QHD, 180Hz, IPS, HDR', 'sku' => 'MON-GB-GS27QA', 'price_cents' => 189990, 'compare_at_price_cents' => 229990, 'stock_quantity' => 9, 'weight_kg' => '4.800', 'accent' => '#10b981', 'short_description' => 'Monitor QHD rápido com visual limpo para jogos competitivos e produtividade.', 'attributes' => ['Tela' => '27 polegadas', 'Resolução' => 'QHD', 'Taxa' => '180Hz', 'HDR' => 'Ready']],
            ['category' => 'Monitores', 'name' => 'Monitor LG UltraWide, 34", WQHD, 160Hz, FreeSync Premium', 'sku' => 'MON-LG-34UW-160', 'price_cents' => 249990, 'compare_at_price_cents' => 289990, 'stock_quantity' => 7, 'weight_kg' => '6.500', 'accent' => '#d946ef', 'short_description' => 'UltraWide para imersão, produtividade e jogos com campo de visão ampliado.', 'attributes' => ['Tela' => '34 polegadas', 'Resolução' => 'WQHD', 'Taxa' => '160Hz', 'Formato' => 'UltraWide']],
            ['category' => 'Periféricos', 'name' => 'Teclado Mecânico Redragon Kumara Pro RGB, Switch Brown, ABNT2', 'sku' => 'KEY-RED-KUMARA-PRO-BR', 'price_cents' => 34990, 'compare_at_price_cents' => 42990, 'stock_quantity' => 40, 'weight_kg' => '0.850', 'accent' => '#dc2626', 'short_description' => 'Teclado mecânico compacto com switches táteis e iluminação RGB.', 'attributes' => ['Layout' => 'ABNT2', 'Switch' => 'Brown', 'Iluminação' => 'RGB', 'Formato' => 'TKL']],
            ['category' => 'Periféricos', 'name' => 'Teclado Logitech G Pro X TKL Lightspeed, RGB, Wireless', 'sku' => 'KEY-LOG-GPROX-TKL', 'price_cents' => 89990, 'compare_at_price_cents' => 104990, 'stock_quantity' => 14, 'weight_kg' => '0.780', 'accent' => '#2563eb', 'short_description' => 'Teclado competitivo sem fio com baixa latência e construção premium.', 'attributes' => ['Conexão' => 'Wireless', 'Formato' => 'TKL', 'Iluminação' => 'RGB', 'Perfil' => 'Competitivo']],
            ['category' => 'Periféricos', 'name' => 'Mouse Logitech G502 X Lightspeed, 25600 DPI, Wireless, Preto', 'sku' => 'MOU-LOG-G502X-LS', 'price_cents' => 59990, 'compare_at_price_cents' => 69990, 'stock_quantity' => 26, 'weight_kg' => '0.110', 'accent' => '#0f172a', 'short_description' => 'Mouse gamer sem fio com sensor preciso, botões programáveis e pegada confortável.', 'attributes' => ['Sensor' => '25600 DPI', 'Conexão' => 'Wireless', 'Botões' => 'Programáveis', 'Peso' => 'Leve']],
            ['category' => 'Periféricos', 'name' => 'Mouse Razer DeathAdder V3 HyperSpeed, 26000 DPI, Wireless', 'sku' => 'MOU-RAZER-DAV3-HS', 'price_cents' => 49990, 'compare_at_price_cents' => 59990, 'stock_quantity' => 31, 'weight_kg' => '0.090', 'accent' => '#22c55e', 'short_description' => 'Mouse ergonômico e leve para jogos FPS e longas sessões.', 'attributes' => ['Sensor' => '26000 DPI', 'Conexão' => 'Wireless', 'Perfil' => 'Ergonômico', 'Uso' => 'FPS']],
            ['category' => 'Periféricos', 'name' => 'Headset HyperX Cloud III Wireless, Drivers 53mm, Preto/Vermelho', 'sku' => 'HEAD-HYPERX-CLOUD3-WL', 'price_cents' => 79990, 'compare_at_price_cents' => 94990, 'stock_quantity' => 18, 'weight_kg' => '0.330', 'accent' => '#b91c1c', 'short_description' => 'Headset wireless confortável com áudio encorpado para jogos e chamadas.', 'attributes' => ['Conexão' => 'Wireless', 'Drivers' => '53mm', 'Microfone' => 'Removível', 'Bateria' => 'Longa duração']],
            ['category' => 'Periféricos', 'name' => 'Controle Xbox Wireless Carbon Black, Bluetooth, USB-C', 'sku' => 'CTRL-XBOX-WL-CARBON', 'price_cents' => 39990, 'compare_at_price_cents' => 45990, 'stock_quantity' => 33, 'weight_kg' => '0.280', 'accent' => '#16a34a', 'short_description' => 'Controle sem fio versátil para PC, console e jogos em nuvem.', 'attributes' => ['Conexão' => 'Bluetooth', 'Porta' => 'USB-C', 'Compatibilidade' => 'PC/Xbox', 'Cor' => 'Carbon Black']],
            ['category' => 'Notebooks', 'name' => 'Notebook Gamer Acer Nitro V15, Intel Core i7, RTX 3050, 512GB SSD, 15.6"', 'sku' => 'NOTE-ACER-NITROV15-I7', 'price_cents' => 519900, 'compare_at_price_cents' => 611110, 'stock_quantity' => 10, 'weight_kg' => '2.100', 'accent' => '#dc2626', 'short_description' => 'Notebook gamer com tela rápida, RTX dedicada e SSD para carregar tudo com agilidade.', 'attributes' => ['Processador' => 'Intel Core i7', 'GPU' => 'RTX 3050', 'SSD' => '512GB', 'Tela' => '15.6 polegadas']],
            ['category' => 'Notebooks', 'name' => 'Notebook Gamer Lenovo LOQ, Intel Core i5, RTX 4050, 16GB RAM, 512GB SSD', 'sku' => 'NOTE-LENOVO-LOQ-I5-4050', 'price_cents' => 579900, 'compare_at_price_cents' => 649990, 'stock_quantity' => 8, 'weight_kg' => '2.400', 'accent' => '#2563eb', 'short_description' => 'Notebook com RTX 4050, boa refrigeração e memória pronta para multitarefa.', 'attributes' => ['Processador' => 'Intel Core i5', 'GPU' => 'RTX 4050', 'RAM' => '16GB', 'SSD' => '512GB']],
            ['category' => 'PC Gamer', 'name' => 'PC Gamer TECHSHOP Ryzen 5 7600, GeForce RTX 4060, 16GB DDR5, SSD 1TB', 'sku' => 'PC-TS-R57600-RTX4060', 'price_cents' => 489990, 'compare_at_price_cents' => 549990, 'stock_quantity' => 6, 'weight_kg' => '10.000', 'accent' => '#ff6500', 'short_description' => 'Computador completo para jogar em Full HD alto com upgrade fácil.', 'attributes' => ['CPU' => 'Ryzen 5 7600', 'GPU' => 'RTX 4060', 'RAM' => '16GB DDR5', 'SSD' => '1TB NVMe']],
            ['category' => 'PC Gamer', 'name' => 'PC Gamer TECHSHOP Ryzen 7 7800X3D, GeForce RTX 4070 Super, 32GB DDR5', 'sku' => 'PC-TS-R77800X3D-4070S', 'price_cents' => 899990, 'compare_at_price_cents' => 999990, 'stock_quantity' => 4, 'weight_kg' => '12.500', 'accent' => '#7c3aed', 'short_description' => 'Máquina premium para QHD alto, streaming e criação de conteúdo.', 'attributes' => ['CPU' => 'Ryzen 7 7800X3D', 'GPU' => 'RTX 4070 Super', 'RAM' => '32GB DDR5', 'SSD' => '2TB NVMe']],
            ['category' => 'Cadeiras Gamer', 'name' => 'Cadeira Gamer ThunderX3 XC3, Reclinável, Até 120kg, Preta', 'sku' => 'CHAIR-TX3-XC3-BLK', 'price_cents' => 129990, 'compare_at_price_cents' => 144433, 'stock_quantity' => 12, 'weight_kg' => '20.000', 'accent' => '#111827', 'short_description' => 'Cadeira reclinável com almofadas para conforto em longas sessões.', 'attributes' => ['Peso suportado' => '120kg', 'Encosto' => 'Reclinável', 'Pistão' => 'Classe 4', 'Cor' => 'Preta']],
        ];
    }

    /**
     * @param  array{short_description: string, attributes: array<string, string>}  $item
     */
    private function descriptionFor(array $item): string
    {
        $features = collect($item['attributes'])
            ->take(4)
            ->map(fn (mixed $value, string $key): string => sprintf(
                '<li><strong>%s:</strong> %s</li>',
                e($key),
                e((string) $value),
            ))
            ->implode('');

        return sprintf(
            '<p>%s</p><p>Selecionado para o catálogo TECHSHOP com foco em performance, pronta entrega e uma experiência de compra objetiva.</p><ul>%s</ul>',
            e((string) $item['short_description']),
            $features,
        );
    }

    private function syncProductImage(Product $product): void
    {
        $imageUrl = $this->imageUrlForSku($product->sku);
        $file = MediaFile::query()->firstOrNew(['path' => $imageUrl]);

        if (! $file->exists) {
            $file->uuid = (string) Str::uuid();
        }

        $file->fill([
            'disk' => 'external',
            'name' => basename(parse_url($imageUrl, PHP_URL_PATH) ?: $product->slug.'.jpg'),
            'mime_type' => 'image/jpeg',
            'size' => null,
            'alt_text' => $product->name,
            'is_active' => true,
        ]);
        $file->save();

        ProductFile::query()->updateOrCreate(
            ['product_id' => $product->id, 'file_id' => $file->id],
            [
                'alt_text' => $product->name,
                'is_primary' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
        );
    }

    private function imageUrlForSku(string $sku): string
    {
        $images = [
            'GPU-MSI-RTX5070-12V2C' => 'https://images7.kabum.com.br/produtos/fotos/725587/placa-de-video-msi-geforce-rtx-5070-12g-ventus-2x-oc-12-gb-gddr7-28gbps-nvidia-geforce-rtx-5070-g5070-12v2c_1743699362_gg.jpg',
            'GPU-ASUS-TUF-RTX5070-O12G' => 'https://images8.kabum.com.br/produtos/fotos/706475/placa-de-video-asus-tuf-gaming-geforce-rtx-5070-oc-12gb-gddr7_1739285538_gg.jpg',
            'GPU-GB-RTX5060TI-16G' => 'https://images7.kabum.com.br/produtos/fotos/725587/placa-de-video-msi-geforce-rtx-5070-12g-ventus-2x-oc-12-gb-gddr7-28gbps-nvidia-geforce-rtx-5070-g5070-12v2c_1743699362_gg.jpg',
            'GPU-RX7800XT-16G' => 'https://images7.kabum.com.br/produtos/fotos/725587/placa-de-video-msi-geforce-rtx-5070-12g-ventus-2x-oc-12-gb-gddr7-28gbps-nvidia-geforce-rtx-5070-g5070-12v2c_1743699362_gg.jpg',
            'CPU-AMD-R79700X' => 'https://images5.kabum.com.br/produtos/fotos/426262/processador-amd-ryzen-7-7800x3d-5-0ghz-max-turbo-cache-104mb-am5-8-nucleos-video-integrado-100-100000910wof_1677590927_gg.jpg',
            'CPU-AMD-R77800X3D' => 'https://images5.kabum.com.br/produtos/fotos/426262/processador-amd-ryzen-7-7800x3d-5-0ghz-max-turbo-cache-104mb-am5-8-nucleos-video-integrado-100-100000910wof_1677590927_gg.jpg',
            'CPU-INTEL-I714700K' => 'https://images3.kabum.com.br/produtos/fotos/497579/processador-intel-core-i7-14700k-14-geracao-5-6ghz-max-turbo-cache-33mb-20-nucleos-28-threads-lga1700-bx8071514700k_1697722324_gg.jpg',
            'MB-MSI-B650M-GPWIFI' => 'https://images3.kabum.com.br/produtos/fotos/523144/placa-mae-asus-tuf-gaming-b760m-plus-wifi-ii-intel-m-atx-ddr5-90mb1hx0-m0eay0_1708521260_gg.jpg',
            'MB-ASUS-B760M-TUFWIFI' => 'https://images3.kabum.com.br/produtos/fotos/523144/placa-mae-asus-tuf-gaming-b760m-plus-wifi-ii-intel-m-atx-ddr5-90mb1hx0-m0eay0_1708521260_gg.jpg',
            'RAM-KF-BEAST-32DDR5-6000' => 'https://images7.kabum.com.br/produtos/fotos/495638/memoria-kingston-fury-beast-rgb-32gb-2x16gb-ddr5-6000mhz-preto-kf560c36bbek2a-32_1736195698_gg.jpg',
            'RAM-CORSAIR-VEN-32DDR5' => 'https://images7.kabum.com.br/produtos/fotos/495638/memoria-kingston-fury-beast-rgb-32gb-2x16gb-ddr5-6000mhz-preto-kf560c36bbek2a-32_1736195698_gg.jpg',
            'SSD-KINGSTON-NV3-1TB' => 'https://images2.kabum.com.br/produtos/fotos/621162/ssd-kingston-nv3-1-tb-m-2-2280-pcie-4-0-nvme-leitura-6000-mb-s-e-gravacao-4000-mb-s-azul-snv3s-1000g_1725622700_gg.jpg',
            'SSD-WD-SN850X-2TB' => 'https://images2.kabum.com.br/produtos/fotos/621162/ssd-kingston-nv3-1-tb-m-2-2280-pcie-4-0-nvme-leitura-6000-mb-s-e-gravacao-4000-mb-s-azul-snv3s-1000g_1725622700_gg.jpg',
            'PSU-CORSAIR-RM850E' => 'https://images.kabum.com.br/produtos/fotos/conteudo/531561/1715972595.jpg',
            'COOLER-RM-AURA-360' => 'https://images8.kabum.com.br/produtos/fotos/447750/water-cooler-gamer-rise-mode-aura-ice-argb-360mm-amd-intel-branco-rm-wai-03-argb_1691417517_gg.jpg',
            'CASE-MONTECH-AIR903MAX' => 'https://images.kabum.com.br/produtos/fotos/conteudo/531561/1715972595.jpg',
            'MON-MSI-MAG275QF' => 'https://images1.kabum.com.br/produtos/fotos/881101/monitor-gamer-msi-mag-275qf-27-qhd-180hz-0-5ms-ips-hdmi-e-displayport-g-sync-e-adaptive-sync-preto-9s6-3cc29h-043_1751046472_gg.jpg',
            'MON-RM-27F1802K' => 'https://images7.kabum.com.br/produtos/fotos/881083/monitor-gamer-rise-mode-prime-27-qhd-180hz-1ms-ips-freesync-hdr-400-hdmi-e-displayport-srgb-110-preto-rm-mog-27f1802k-b_1751370354_gg.jpg',
            'MON-GB-GS27QA' => 'https://images1.kabum.com.br/produtos/fotos/881101/monitor-gamer-msi-mag-275qf-27-qhd-180hz-0-5ms-ips-hdmi-e-displayport-g-sync-e-adaptive-sync-preto-9s6-3cc29h-043_1751046472_gg.jpg',
            'MON-LG-34UW-160' => 'https://images7.kabum.com.br/produtos/fotos/881083/monitor-gamer-rise-mode-prime-27-qhd-180hz-1ms-ips-freesync-hdr-400-hdmi-e-displayport-srgb-110-preto-rm-mog-27f1802k-b_1751370354_gg.jpg',
            'KEY-RED-KUMARA-PRO-BR' => 'https://images5.kabum.com.br/produtos/fotos/662635/teclado-mecanico-gamer-redragon-phantom-rgb-chroma-mk-ii-switch-marrom-abnt2-preto-k629-rgb-pt-brown-_1733425219_gg.jpg',
            'KEY-LOG-GPROX-TKL' => 'https://images5.kabum.com.br/produtos/fotos/662635/teclado-mecanico-gamer-redragon-phantom-rgb-chroma-mk-ii-switch-marrom-abnt2-preto-k629-rgb-pt-brown-_1733425219_gg.jpg',
            'MOU-LOG-G502X-LS' => 'https://images3.kabum.com.br/produtos/fotos/519942/mouse-sem-fio-gamer-logitech-g502-x-lightspeed-rgb-25600-dpi-hybrid-switches-13-botoes-preto-910-006178_1707490031_gg.jpg',
            'MOU-RAZER-DAV3-HS' => 'https://images0.kabum.com.br/produtos/fotos/621830/mouse-gamer-sem-fio-razer-deathadder-v3-hyperspeed-26000-dpi-sensor-focus-x-5g-preto-rz01-05140100-r3u1_1726775619_gg.jpg',
            'HEAD-HYPERX-CLOUD3-WL' => 'https://images6.kabum.com.br/produtos/fotos/536014/headset-gamer-sem-fio-hyperx-cloud-iii-drivers-53mm-wireless-multi-plataforma-preto-e-vermelho-77z46aa_1713453547_gg.jpg',
            'CTRL-XBOX-WL-CARBON' => 'https://images7.kabum.com.br/produtos/fotos/128647/controle-sem-fio-xbox-carbon-black_1605207861_gg.jpg',
            'NOTE-ACER-NITROV15-I7' => 'https://images3.kabum.com.br/produtos/fotos/649716/notebook-acer-gamer-nitro-v15-intel-core-i7-13620h-8gb-ram-rtx-3050-ssd-512gb-15-6-fhd-ips-144hz-linux-gutta-anv15-51-7037_1738598787_gg.jpg',
            'NOTE-LENOVO-LOQ-I5-4050' => 'https://images3.kabum.com.br/produtos/fotos/649716/notebook-acer-gamer-nitro-v15-intel-core-i7-13620h-8gb-ram-rtx-3050-ssd-512gb-15-6-fhd-ips-144hz-linux-gutta-anv15-51-7037_1738598787_gg.jpg',
            'PC-TS-R57600-RTX4060' => 'https://images7.kabum.com.br/produtos/fotos/725587/placa-de-video-msi-geforce-rtx-5070-12g-ventus-2x-oc-12-gb-gddr7-28gbps-nvidia-geforce-rtx-5070-g5070-12v2c_1743699362_gg.jpg',
            'PC-TS-R77800X3D-4070S' => 'https://images7.kabum.com.br/produtos/fotos/725587/placa-de-video-msi-geforce-rtx-5070-12g-ventus-2x-oc-12-gb-gddr7-28gbps-nvidia-geforce-rtx-5070-g5070-12v2c_1743699362_gg.jpg',
            'CHAIR-TX3-XC3-BLK' => 'https://images6.kabum.com.br/produtos/fotos/955296/cadeira-gamer-thunderx3-yama1-ergonomica-preta-706247_1756990757_gg.jpg',
        ];

        return $images[$sku] ?? $images['GPU-MSI-RTX5070-12V2C'];
    }

    private function syncReviews(Product $product, int $index): void
    {
        $names = ['Mariana Alves', 'Rafael Costa', 'Bianca Souza', 'Lucas Martins', 'Paulo Henrique', 'Camila Rocha'];
        $templates = [
            [5, 'Excelente compra', 'Chegou bem embalado, funcionou de primeira e entregou a performance esperada.'],
            [5, 'Produto muito bom', 'Acabamento consistente e desempenho acima do que eu imaginava para o meu setup.'],
            [4, 'Vale o preço', 'Gostei bastante. Só senti falta de uma embalagem interna mais reforçada.'],
            [5, 'Recomendo', 'Entrega rápida e produto exatamente como anunciado na loja.'],
            [4, 'Bom custo-benefício', 'Atendeu meu uso diário e ainda sobrou margem para upgrades futuros.'],
        ];

        ProductReview::query()->whereBelongsTo($product)->delete();

        for ($offset = 0; $offset < 4; $offset++) {
            [$rating, $title, $comment] = $templates[($index + $offset) % count($templates)];

            ProductReview::query()->create([
                'product_id' => $product->id,
                'customer_name' => $names[($index + $offset) % count($names)],
                'rating' => $rating,
                'title' => $title,
                'comment' => $comment,
                'is_approved' => true,
                'created_at' => now()->subDays(($index * 2) + $offset),
                'updated_at' => now()->subDays(($index * 2) + $offset),
            ]);
        }
    }
}
