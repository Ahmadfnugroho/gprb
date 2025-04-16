<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;


class Bundling extends Model
{
    use LogsActivity;

    protected $fillable = ['name', 'price'];
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'price']);
    }


    public static function boot()
    {
        parent::boot();


        static::creating(function ($bundling) {
            // Format custom_id dengan prefix '1' dan ID unik
            $bundling->custom_id = 'bundling-' . (Bundling::max('id') + 1);
        });
    }

    public function detailTransactions()
    {
        return $this->hasMany(DetailTransaction::class);
    }

    public function transactions()
    {
        return $this->hasManyThrough(
            Transaction::class, // Model tujuan (Transaction)
            DetailTransaction::class, // Model perantara (DetailTransaction)
            'bundling_id', // Foreign key di tabel DetailTransaction
            'id', // Foreign key di tabel Transaction
            'id', // Local key di tabel Bundling
            'transaction_id' // Local key di tabel DetailTransaction
        );
    }


    public function products()
    {
        return $this->belongsToMany(Product::class, 'bundling_products', 'bundling_id', 'product_id')->withPivot('id');
    }
}
