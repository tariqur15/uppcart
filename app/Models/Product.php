<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'brand_id',
        'collection_id',
        'featured',
        'name',
        'slug',
        'sku',
        'product_type',
        'description',
        'short_description',
        'long_description',
        'price',
        'cost_price',
        'sale_price',
        'stock',
        'show_stock',
        'initial_stock',
        'weight',
        'image',
        'gallery_images',
        'type',
        'digital_files',
        'download_limit',
        'download_expiry',
        'status',
        'variants_enabled',
        'variant_options',
        'variant_data',
        'custom_fields_enabled',
        'custom_fields',
        'seo_title',
        'seo_description',
        'seo_slug',
    ];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
            'digital_files' => 'array',
            'featured' => 'boolean',
            'variants_enabled' => 'boolean',
            'show_stock' => 'boolean',
            'variant_options' => 'array',
            'variant_data' => 'array',
            'custom_fields_enabled' => 'boolean',
            'custom_fields' => 'array',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function brands(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'brand_product');
    }

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_products')
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function attributeOptions(): BelongsToMany
    {
        return $this->belongsToMany(AttributeOption::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function productViews(): HasMany
    {
        return $this->hasMany(ProductView::class);
    }

    public function tierPrices(): HasMany
    {
        return $this->hasMany(ProductTierPrice::class);
    }

    public function isInStock(): bool
    {
        return $this->type === 'digital' || $this->stock > 0;
    }
}
