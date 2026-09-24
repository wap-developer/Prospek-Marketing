<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProspectSource extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class, 'source_id');
    }
}
