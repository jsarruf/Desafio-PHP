<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $fillable = ['name','is_active','priority','config'];
    protected $casts = ['is_active'=>'boolean','config'=>'array'];

    public function scopeActive($q){ return $q->where('is_active', true); }
}
