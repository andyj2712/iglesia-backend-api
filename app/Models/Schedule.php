<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model {
    protected $fillable = 
    ['ministry_id',
     'user_id', 
     'date',
     'start_time', 
     'task', 
     'status'];

    public function ministry() {
        return $this->belongsTo(Ministry::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
