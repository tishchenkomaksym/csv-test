<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public bool $timestamps = false;

    protected string $table = 'products';

    protected array $fillable = [
        'name',
        'description',
        'code',
        'added_at',
        'discontinued_at',
        'price',
        'stoke_level',
        'updated_at'
    ];

    protected array $casts = [
        'added_at' => 'datetime:Y-m-d h:i:s',
        'discontinued_at' => 'datetime:Y-m-d h:i:s',
        'updated_at' => 'timestamp'
    ];

}
