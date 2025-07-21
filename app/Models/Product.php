<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // ✅ 1. ADD THIS USE STATEMENT

class Product extends Model
{
     use HasFactory;
    protected $fillable = [
        'generic_name',
        'brand_name',
        'form',
        'strength',
        'img_file_path',
        'season_peak',
        'trend_score',
    ];

    /**
     * The accessors to append to the model's array form.
     * This automatically adds 'image_url' to your API response.
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * Get the full public URL to the product's image.
     *
     * @return string
     */
    public function getImageUrlAttribute(): string
    {
        // ✅ **THE FIX:** This logic checks if a specific image path exists in the `public` folder.
        // If it does, it uses the asset() helper to create a full URL.
        // This assumes 'img_file_path' stores a value like 'products/your_image.png'.
        if ($this->img_file_path && file_exists(public_path($this->img_file_path))) {
            return asset($this->img_file_path);
        }

        // If no image is found, return the URL for a default placeholder image.
        // Make sure 'default-product-pic.png' is in your 'public/image/' directory.
        return asset('image/default-product-pic.png');
    }

    public function scopeForSeason($query, $season)
    {
        return $query->where('season_peak', $season)
                     ->orWhere('season_peak', 'all-year');
    }

    public function calculateTrendScore($currentSeason)
    {
        $score = $this->orders()->where('status', 'delivered')->count();
        
        // Bonus if selling off-season
        if ($this->season_peak !== $currentSeason && $this->season_peak !== 'all-year') {
            $score *= 1.25;
        }
        
        $this->trend_score = $score;
        $this->save();
        
        return $score;
    }

    public function orders()
    {
        return $this->hasManyThrough(
            Order::class,
            ExclusiveDeal::class,
            'product_id',
            'exclusive_deal_id',
            'id',
            'id'
        );
    }
    public function inventories() {
        return $this->hasMany(Inventory::class);
    }

    public function exclusive_deals() {
        return $this->hasMany(ExclusiveDeal::class);
    }
        public function exclusiveDeals()
    {
        return $this->hasMany(ExclusiveDeal::class);
    }
}