<?php

namespace App\Services;

use App\Jobs\ProcessPdfJob;
use App\Models\Book;
use App\Models\BookPage;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SemanticService
{
    const API_MAKE_EMBEDDING = 'embedding/make';
    const API_COMPARE_EMBEDDING = 'embedding/compare';

    /**
     * Cliente Guzzle
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Cria uma nova instância da classe
     * 
     * @return void
     */
    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => config('services.semantic.url'),
            'timeout' => config('services.semantic.timeout'),
            'verify' => false
        ]);
    }

    /**
     * Prepara os cabeçalhos padrões a serem enviados na requisição.
     *
     * @param ?array $headers
     * @return array
     */
    protected function getHeaders($headers = [])
    {
        return array_merge($headers, [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'User-Agent' => "LibroVault Client"
        ]);
    }

    /**
     * Faz uma requisição ao Microserviço de Embedding
     *
     * @param string $method
     * @param string $uri
     * @param array $data
     * @param ?array $options
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function fetch($method, $uri, $data = [], $options = [])
    {
        try {
            //logger($data);
            //logger(json_encode($data));
            $opts = array_merge_recursive($options, [
                'headers' => $this->getHeaders()
            ]);

            if (!empty($data)) {
                if (Str::upper($method) == 'GET')
                    $opts['query'] = $data;
                else
                    $opts['json'] = $data;
            }

            $response = $this->client->request($method, $uri, $opts);
            return $response;
        } catch (RequestException $th) {
            // TODO: Elaborar ações necessárias em caso de erro

            throw $th;
        }
    }

    /**
     * Transforma uma resposta em JSON.
     * 
     * @param \Psr\Http\Message\ResponseInterface $response
     * @return array
     */
    static public function responseToJson($response)
    {
        return json_decode($response->getBody(), true)['body'];
    }

    /**
     * Faz o processamento assíncrono de um arquivo PDF para gerar embeddings de cada página.
     * 
     * @param \App\Models\Book $book
     * @return \App\Models\Book
     */
    public function processPdf(Book $book)
    {
        if (!$book->bookFile) {
            throw new \Exception("Livro {$book->id} não possui um arquivo PDF para processar.");
        }

        // Chama o job ProcessPdfJob para processar o PDF
        ProcessPdfJob::dispatch($book);

        return $book;
    }

    /**
     * Retorna o endpoint da API de acordo com a chave informada.
     * @param string $key
     * @return string
     */
    public function getApiEndpoint(string $key): string
    {
        return match ($key) {
            'make_embedding' => self::API_MAKE_EMBEDDING,
            'compare_embedding' => self::API_COMPARE_EMBEDDING,
            default => throw new \Exception("API key inválida."),
        };
    }

    /**
     * Realiza uma busca semântica utilizando embeddings.
     *
     * @param string $query
     * @param int $maxResults
     * @param int $chunkSize
     * @param int $timeout
     * @return array
     */
    public function advanceSearch(string $query, int $maxResults = 10, int $chunkSize = 100, int $timeout = 30): array
    {
        // Obtém todas as páginas com embeddings armazenados
        $pages = BookPage::with('embeddingFile')->get();

        // Monta a lista de embeddings disponíveis
        $allEmbeddings = [];
        foreach ($pages as $page) {
            if ($page->embeddingFile) {
                $allEmbeddings[] = [
                    'page_number' => $page->page_number,
                    'book_id' => $page->book_id,
                    'embedding' => Storage::disk('local')->path($page->embeddingFile->path),
                ];
            }
        }

        // Divide os embeddings em chunks menores
        $chunks = array_chunk($allEmbeddings, $chunkSize);

        // Variável para armazenar os melhores resultados
        $topResults = [];
        $timeProcessing = 0;
        foreach ($chunks as $chunk) {
            $timeStartChunk = microtime(true);
            /**
             * Exemplo de chunk
             * 
             * [
             *    [
             *      'page_number' => 1,
             *      'book_id' => 1,
             *      'embedding' => '/path/to/embedding1.json',
             *     ],
             *     ...
             */

            // Envia a chunk para o microsserviço e obtém a resposta
            $response = $this->fetch('POST', self::API_COMPARE_EMBEDDING, [
                'query' => $query,
                'embeddings' => $chunk,
            ]);

            // Converte a resposta para JSON
            $ranking = $this->responseToJson($response) ?? [];

            // Junta os resultados e mantém apenas os melhores
            $topResults = $this->mergeAndRankResults($topResults, $ranking, $maxResults);
            $timeEndChunk = microtime(true);

            $timeProcessing += $timeEndChunk - $timeStartChunk;

            // Se o tempo de processamento ultrapassar o tempo de timeout aceitável, para a execução
            if ($timeProcessing > $timeout) {
                break;
            }
        }

        // Busca as `BookPage` dos 10 melhores embeddings
        $bestPages = $this->getBestBookPages($topResults);

        return $bestPages;
    }

    /**
     * Junta os rankings parciais e mantém apenas os melhores resultados.
     *
     * @param array $currentResults
     * @param array $newResults
     * @param int $maxResults
     * @return array
     */
    private function mergeAndRankResults(array $currentResults, array $newResults, int $maxResults): array
    {
        // Junta os resultados
        $merged = array_merge($currentResults, $newResults);

        // Ordena por similaridade (maior primeiro)
        usort($merged, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        // Mantém apenas os `X` melhores
        return array_slice($merged, 0, $maxResults);
    }

    /**
     * Obtém as `BookPage` correspondentes aos melhores resultados.
     *
     * @param array $topResults
     * @return array
     */
    private function getBestBookPages(array $topResults): array
    {
        $bookPages = [];

        foreach ($topResults as $result) {
            $bookPage = BookPage::where('book_id', $result['book_id'])
                ->where('page_number', $result['page_number'])
                ->first();

            if ($bookPage) {
                $bookPages[] = [
                    'book_id' => $bookPage->book_id,
                    'page_number' => $bookPage->page_number,
                    'text' => $bookPage->text,
                    'similarity' => $result['similarity'],
                ];
            }
        }

        return $bookPages;
    }
}
