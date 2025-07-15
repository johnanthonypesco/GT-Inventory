<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
