<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id','title','type','bedrooms','bathrooms','area','price','status'
    ];

    public function project()
{
    return $this->belongsTo(Project::class, 'project_id');
}

}
