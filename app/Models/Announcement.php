<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'ministry_id',
        'user_id',
        'title',
        'content',
        'image_path'
    ];

    // Saber quién publicó el anuncio
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Saber a qué ministerio pertenece (si es null, es general)
    public function ministry() {
        return $this->belongsTo(Ministry::class);
    }
}