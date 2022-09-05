<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
    	'name'
    ];

    protected $casts = [
    	'name' => 'string'
    ];

    public function Products()
    {
        return $this->hasMany(Product::class);
    }
}
