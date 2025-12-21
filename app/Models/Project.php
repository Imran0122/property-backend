<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'title','slug','city_id','location','developer','description','status','cover_image'
    ];

    public function city() {
        return $this->belongsTo(City::class);
    }

    public function units() {
        return $this->hasMany(ProjectUnit::class);
    }
}
