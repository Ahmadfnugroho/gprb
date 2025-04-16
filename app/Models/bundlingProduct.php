<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class bundlingProduct extends Model
{
    public function Bundlings()
    {
        return $this->hasMany(Bundling::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
