<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Post extends Model
{
    use HasFactory, Searchable;

    use \App\Traits\ObserverTrait;

    protected $fillable = [
        'title',
        'name',
    ];

    protected $casts = [
        'title' => 'string',
        'name' => 'string',
        'created_at' => 'datetime: Y-m-d H:i:s',
        'updated_at' => 'datetime: Y-m-d H:i:s',
    ];

    public static $audit = [
        //要紀錄欄位
        'only' => [
            'title',
            'name',
        ],           
    ];  
}   
