<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    /** @use HasFactory<\Database\Factories\FileFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'type',
        'size',
        'extension',
        'expires_in',
    ];

    protected $casts = [
        'expires_in' => 'datetime',
    ];
}
