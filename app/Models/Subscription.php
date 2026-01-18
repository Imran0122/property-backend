<?php
namespace App\Models;

class Subscription extends Model
{
    protected $fillable = ['user_id','package_id','starts_at','ends_at','used_properties','used_featured','status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->ends_at >= now();
    }
}
