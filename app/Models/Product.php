<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Product extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'quantity',
        'price',
        'thumbnail',
        'status',
        'slug',
        'category_id',
        'brand_id',
        'sub_category_id',
        'premiere',
    ];

    protected $casts = [
        'price' => MoneyCast::class,
    ];


    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'text']);
    }

    public function setNameAttribute($value)
    {
        // Jika nilai slug tidak diberikan, generate slug dari nm_produk
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }





    public static function boot()
    {
        parent::boot();

        static::saving(function ($product) {

            // Memperbarui status berdasarkan available_quantity dan quantity
            if ($product->quantity <= 0) {
                $product->status = 'unavailable';
            } else {
                $product->status = 'available';
            }
        });

        static::creating(function ($product) {
            // Format custom_id dengan prefix '1' dan ID unik
            $product->custom_id = 'produk-' . (Product::max('id') + 1);
        });
    }





    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }



    public function rentalIncludes(): HasMany
    {
        return $this->hasMany(RentalInclude::class);
    }

    public function productSpecifications(): HasMany
    {
        return $this->hasMany(ProductSpecification::class);
    }

    public function productPhotos(): HasMany
    {
        return $this->hasMany(ProductPhoto::class);
    }

    public function detailTransactions(): HasMany
    {
        return $this->hasMany(DetailTransaction::class);
    }


    public function transactions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Transaction::class, // Model tujuan (Transaction)
            DetailTransaction::class, // Model perantara (DetailTransaction)
            'product_id', // Foreign key di tabel DetailTransaction
            'id', // Foreign key di tabel Transaction
            'id', // Local key di tabel Product
            'transaction_id' // Local key di tabel DetailTransaction
        );
    }


    public function bundlings()
    {
        return $this->belongsToMany(Bundling::class, 'bundling_products', 'product_id', 'bundling_id')->withPivot('id');
    }



    public function rentalIncludeTransactions(): HasManyThrough
    {
        return $this->hasManyThrough(
            RentalInclude::class,
            Product::class,
            'id', // foreign key di Product
            'include_product_id' // foreign key di RentalInclude
        );
    }
}
