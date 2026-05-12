<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['name', 'type'])]
final class Status extends Model
{
    public const string TYPE_ORDER = 'order';

    public const string TYPE_PRODUCT = 'product';

    public const string TYPE_USER = 'user';

    /**
     * @param  Builder<Status>  $query
     */
    #[Scope]
    protected function forType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }
}
