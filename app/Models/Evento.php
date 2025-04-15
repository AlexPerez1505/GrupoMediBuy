<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evento extends Model {
    use HasFactory;

    protected $fillable = [
        'title', 'location', 'all_day', 'start', 'end', 
        'repeat', 'repeat_end', 'guests', 'alert', 'url', 'notes'
    ];
}
