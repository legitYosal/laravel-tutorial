<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductPicture extends Model
{
    use HasFactory;
    protected $fillable = [
        'image_name',
        'image_path',
        'product_id',
    ];
    public static function save_and_create($file, $product_id)
    {
        $path = $file->store('public/files/product/images/');
        $name = $file->getClientOriginalName();

        return ProductPicture::create([
            'image_path' => $path,
            'image_name' => $name,
            'product_id' => $product_id,
        ]);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
