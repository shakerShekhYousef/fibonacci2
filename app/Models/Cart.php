<?php

namespace App\Models;

use App\Models\Traits\Cart\CartRelations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory, CartRelations;

    protected $guarded = [];
}
