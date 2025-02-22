<?php

namespace App\Models;

use App\Enums\Status;
use App\Facades\FileManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'thumbnail_id',
        'pdf_id',
        'title',
        'volume',
        'edition',
        'pages',
        'isbn',
        'author',
        'genre',
        'publisher',
        'description',
        'year',
        'processing_status',
    ];

    protected $casts = [
        'year' => 'integer',
        'processing_status' => Status::class,
    ];

    // Função de boot para observar eventos do Eloquent
    protected static function boot(): void
    {
        parent::boot();

        // Deletar o livro e os arquivos relacionados
        static::deleting(function (Book $book): void {
            $book->deleteWithFiles();
        });
    }

    /**
     * Relacionamento com o usuário (User)
     * @return BelongsTo<User, Book>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com a thumbnail do livro (File)
     * @return BelongsTo<File, Book>
     */
    public function thumbnailFile(): BelongsTo
    {
        return $this->belongsTo(File::class, 'thumbnail_id');
    }

    /**
     * Relacionamento com o arquivo PDF do livro (File)
     * @return BelongsTo<File, Book>
     */
    public function pdfFile(): BelongsTo
    {
        return $this->belongsTo(File::class, 'pdf_id');
    }

    /**
     * Relacionamento com as páginas do livro (BookPage)
     * @return HasMany<BookPage, Book>
     */
    public function bookPages(): HasMany
    {
        return $this->hasMany(BookPage::class);
    }


    // Excluir arquivos relacionados ao deletar um livro
    public function deleteWithFiles(): void
    {
        // Deletar a thumbnail se existir
        if ($this->thumbnailFile instanceof File) {
            FileManager::delete($this->thumbnailFile);
        }

        // Deletar o arquivo PDF se existir
        if ($this->pdfFile instanceof File) {
            FileManager::delete($this->pdfFile);
        }

        // Deletar as páginas do livro
        $this->bookPages->each(fn($page) => $page->delete());
    }
}
