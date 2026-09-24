<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProspectStatus extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'name'];

    public function prospects(): HasMany
    {
        return $this->hasMany(Prospect::class, 'status_id');
    }
}
