<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Control extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function permit()
    {
        return $this->belongsTo('\App\Models\Permitt');
    }
}
