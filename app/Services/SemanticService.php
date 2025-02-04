<?php

namespace App\Services;

use App\Models\Book;
use GuzzleHttp\Exception\RequestException;
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
}
