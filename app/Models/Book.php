<?php

namespace App\Models;

use App\Enums\Genre;
use App\Facades\FileManager;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Book extends Model
{
    /** @use HasFactory<BookFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'thumbnail_id',
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
    ];

    protected $casts = [
        'year' => 'integer',
        'genre' => Genre::class,
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
    public function bookFile(): BelongsTo
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
    private function deleteWithFiles(): void
    {
        // Deletar a thumbnail se existir
        if ($this->thumbnailFile instanceof File) {
            FileManager::delete($this->thumbnailFile);
        }

        // Deletar o arquivo PDF se existir
        if ($this->bookFile instanceof File) {
            FileManager::delete($this->bookFile);
        }

        // Deletar as páginas do livro
        $this->bookPages->each(fn($page) => $page->delete());
    }
}
