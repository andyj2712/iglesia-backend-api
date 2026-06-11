<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ministry extends Model {
    protected $fillable = ['name', 'description'];

    public function users() {
        return $this->belongsToMany(User::class);
    }

    public function schedules() {
        return $this->hasMany(Schedule::class);
    }

    public function announcements() {
        return $this->hasMany(Announcement::class);
    }
}
