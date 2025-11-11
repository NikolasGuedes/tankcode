<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;



class Room extends Model
{

    protected $table = 'rooms';
    // Apenas os dois campos que você deseja: name_room e code
    protected $fillable = [
        'name_room',
        'cod',
    ];

    public function students()
    {
        return $this->hasMany(Student::class);
    }

  
    // Removi protected $casts
}