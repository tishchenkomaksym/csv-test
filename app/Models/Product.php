<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * @var bool
     */
    public bool $timestamps = false;

    /**
     * @var string
     */
    protected string $table = 'products';

    /**
     * @var array|string[]
     */
    protected array $fillable = [
        'name',
        'description',
        'code',
        'added_at',
        'discontinued_at',
        'price',
        'stock',
        'updated_at'
    ];

    /**
     * @var array|string[]
     */
    protected array $casts = [
        'added_at' => 'datetime:Y-m-d h:i:s',
        'discontinued_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'timestamp'
    ];

}
