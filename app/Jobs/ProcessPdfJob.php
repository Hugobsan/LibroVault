<?php

namespace App\Jobs;

use App\Enums\Status;
use App\Facades\FileManager;
use App\Models\Book;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Facades\SemanticManager;
use App\Models\BookPage;
use App\Models\File;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class ProcessPdfJob implements ShouldQueue
{
    use Queueable;
    protected Book $book;

    /**
     * Create a new job instance.
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $this->book->update(['processing_status' => Status::PROCESSING]);
            $pdfFile = $this->book->bookFile;

            if (!$pdfFile) {
                logger()->error("O livro {$this->book->id} não possui um arquivo PDF associado.");
                return;
            }

            $pdfPath = FileManager::getPath($pdfFile->path);

            $pages = $this->splitPdfIntoPages($pdfPath);

            foreach ($pages as $pageNumber => $pageContent) {
                // Envia cada página ao microsserviço para gerar embedding
                $response = SemanticManager::fetch('POST', SemanticManager::getApiEndpoint('make_embedding'), [
                    'text' => $pageContent,
                    'book_id' => $this->book->id,
                    'page_number' => $pageNumber,
                ])->getBody()->getContents();

                $embedding = SemanticManager::responseToJson($response);
                $embeddingPath = "embeddings/book_{$this->book->id}_page_{$pageNumber}.json";

                Storage::disk('local')->put($embeddingPath, json_encode($embedding));

                $embeddingFile = File::create([
                    'name' => "embedding_book_{$this->book->id}_page_{$pageNumber}.json",
                    'path' => $embeddingPath,
                    'type' => 'application/json',
                    'size' => strlen(json_encode($embedding)),
                    'extension' => 'json',
                ]);

                BookPage::create([
                    'book_id' => $this->book->id,
                    'page_number' => $pageNumber,
                    'text' => $pageContent,
                    'embedding_file_id' => $embeddingFile->id,
                ]);
            }

            $this->book->update(['processing_status' => Status::COMPLETED]);
        } catch (\Exception $e) {
            $this->book->update(['processing_status' => Status::FAILED]);
            logger()->error("Erro ao processar o arquivo PDF do livro {$this->book->id}: {$e->getMessage()}");
        }
    }

    private function splitPdfIntoPages($pdfPath): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $pages = $pdf->getPages();

        $pageTexts = [];
        foreach ($pages as $index => $page) {
            $pageTexts[$index + 1] = trim($page->getText());
        }

        return $pageTexts;
    }
}
