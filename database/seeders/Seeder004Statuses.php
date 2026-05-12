<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Seeder;

final class Seeder004Statuses extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            Status::TYPE_ORDER => [
                'Pendente',
                'Pago',
                'Enviado',
                'Nao entregue',
                'Concluido',
                'Cancelado',
                'Devolucao solicitada',
                'Devolvido',
                'Reembolso solicitado',
                'Reembolsado',
            ],
        ];

        foreach ($statuses as $type => $names) {
            foreach ($names as $name) {
                Status::query()->updateOrCreate(
                    ['type' => $type, 'name' => $name],
                );
            }
        }
    }
}
