<?php

namespace App\Models;

use App\Facades\FileManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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

    protected $appends = [
        'url',
    ];

    /**
     * Retorna a URL pública gerada para o arquivo.
     *
     * @return string
     */
    public function getUrlAttribute(): string
    {
        return FileManager::getUrl($this);
    }
}
