<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'user_id',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function images()
    {
        return $this->hasMany(ProductPicture::class);
    }
    public function prices()
    {
        return $this->hasMany(ProductPrice::class);
    }
    public function likes()
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    private $last_selling_price_sql = '(select product_prices.selling_price from product_prices
    where product_prices.product_id = products.id 
    order by created_at desc limit 1)';
    public function scopeSelectPrice($query)
    {
        return $query->selectRaw(
            '*, '.$this->last_selling_price_sql.' as selling_price'
        );
    }
    public function scopeLowLimit($query, $low_limit)
    {
        return $query->whereRaw(
            $this->last_selling_price_sql.' >= '.$low_limit);
    }
    public function scopeHighLimit($query, $high_limit)
    {
        return $query->whereRaw(
            $this->last_selling_price_sql.' <= '.$high_limit
        );
    }
}
