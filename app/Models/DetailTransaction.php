<?php

namespace App\Models;

use App\Casts\MoneyCast;
use Carbon\Carbon;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Log;

class DetailTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'product_id',
        'quantity',
        'bundling_id',
        'available_quantity',
        'price',
        'total_price',
    ];

    protected $casts = [
        'price' => MoneyCast::class,
        'total_price' => MoneyCast::class,

    ];







    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }


    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function bundling()
    {
        return $this->belongsTo(Bundling::class);
    }

    public function rentalIncludes(): HasManyThrough
    {
        return $this->hasManyThrough(
            RentalInclude::class,
            Product::class,
            'id',
            'include_product_id'
        );
    }
}
