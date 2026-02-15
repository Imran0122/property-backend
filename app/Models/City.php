<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // 🔗 Relationships
    public function properties()
    {
        return $this->hasMany(Property::class);
    }
    public function societies(){
    return $this->hasMany(Society::class);

    
}
public function areas()
{
    return $this->hasMany(Area::class);
}
}
