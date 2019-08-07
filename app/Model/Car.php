<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'name', 'image_url', 'delay', 'price', 'color', 'upholstery', 'combustible', 'consumo_mixto', 'emisiones_de', 'reserved', 'options'
    ];
}
