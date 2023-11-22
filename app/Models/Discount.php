<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Orchid\Filters\Filterable;

class Discount extends Model
{
    use HasFactory, AsSource, Filterable;

    /**
     * @var array
     */
    protected $allowedSorts = [
        'id',
        'name',
        'location',
    ];
}
