<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permitt extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = [
        'oksigen' => 'double',
        'karbon_dioksida' => 'double',
        'hidrogen_sulfida' => 'double',
        'lel' => 'double'
    ];

    public function workPreparation()
    {
        return $this->hasMany('\App\Models\WorkPreparation');
    }

    public function hazard()
    {
        return $this->hasMany('\App\Models\HazardIdentification');
    }

    public function control()
    {
        return $this->hasMany('\App\Models\Control');
    }

    public function user()
    {
        return $this->belongsTo('\App\Models\User');
    }

    public function history()
    {
        return $this->hasMany('\App\Models\History');
    }

    public function document()
    {
        return $this->hasOne('\App\Models\Document')->latest('created_at');
    }

    public function housekeeping()
    {
        return $this->hasMany('\App\Models\Housekeeping');
    }
}
