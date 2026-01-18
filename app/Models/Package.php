<?php
namespace App\Models;
class Package extends Model
{
    protected $fillable = ['name','price','property_limit','featured_limit','duration_days'];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }
}
