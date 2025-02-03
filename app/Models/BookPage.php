<?php

namespace App\Models;

use App\Facades\FileManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookPage extends Model
{
    /** @use HasFactory<\Database\Factories\BookPageFactory> */
    use HasFactory;
    
    protected $fillable = [
        'book_id',
        'page_number',
        'text',
        'embedding_file_id',
    ];

    // Relacionamento com o Livro (Book)
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    // Relacionamento com o arquivo de embedding (File)
    public function embeddingFile(): BelongsTo
    {
        return $this->belongsTo(File::class, 'embedding_file_id');
    }

    // Função de boot para observar eventos do Eloquent
    protected static function boot()
    {
        parent::boot();

        // Deletar os arquivos relacionados
        static::deleted(function (BookPage $bookPage): void {
            FileManager::delete($bookPage->embedding_file);
        });
    }
}
