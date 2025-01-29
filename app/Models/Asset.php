<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;
    public function base()
    {
        return $this->belongsTo(Base::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
}
