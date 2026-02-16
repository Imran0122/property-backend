<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Society extends Model
{
    protected $fillable = [
        'city_id','name','slug','image','description','is_popular'
    ];

    public function city(){
        return $this->belongsTo(City::class);
    }

 public function images()
{
    return $this->hasMany(SocietyImage::class, 'society_id');
}
}
