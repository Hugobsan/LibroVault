<?php

namespace App\Services;

use App\Models\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class FileService
{
    /**
     * Upload de arquivo no storage local
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param int|null $daysTilExpire
     * @return File
     */
    public function upload(UploadedFile $file, string $directory = 'uploads', int $daysTilExpire = null): File
    {
        $path = $file->store($directory, 'local');

        return File::create([
            // Removendo caracteres especiais do nome do arquivo
            'name' => preg_replace('/[^A-Za-z0-9_\-]/', '_', $file->getClientOriginalName()),
            'path' => $path,
            'type' => $file->getMimeType(),
            'size' => $file->getSize(),
            'extension' => $file->getClientOriginalExtension(),
            'expires_in' => $daysTilExpire ? Carbon::now()->addDays($daysTilExpire) : null,
        ]);
    }

    public function delete(File $file): bool
    {
        Storage::disk('local')->delete($file->path);
        return $file->delete();
    }

    /**
     * Retorna a URL do arquivo no storage local
     * @param \App\Models\File $file
     * @return string
     */
    public function getUrl(File $file): string
    {
        return Storage::disk('local')->url($file->path);
    }

    /**
     * Retorna o caminho do arquivo no storage local
     * @param \App\Models\File $file
     * @return string
     */
    public function getPath(File $file): string
    {
        return Storage::disk('local')->path($file->path);
    }
}
