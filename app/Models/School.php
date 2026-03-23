<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'cnpj',
        'logo_path',
        'status',
    ];

    public function pointOfSchools(): HasMany
    {
        return $this->hasMany(PointOfSchool::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
