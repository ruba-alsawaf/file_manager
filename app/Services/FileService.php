<?php

namespace App\Services;

use App\Repositories\Interfaces\FileRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    public function __construct(
        private FileRepositoryInterface $fileRepository
    ) {}

    public function uploadFile(UploadedFile $file, int $folderId)
    {
        $path = $file->store("files/{$folderId}");
        
        return $this->fileRepository->create([
            'name' => $file->getClientOriginalName(),
            'path' => $path,
            'folder_id' => $folderId,
        ]);
    }

    public function getFile($id)
    {
        return $this->fileRepository->find($id);
    }

    public function updateFile($id, array $data)
    {
        return $this->fileRepository->update($id, $data);
    }

    public function deleteFile($id)
    {
        $file = $this->fileRepository->find($id);
        Storage::delete($file->path);
        return $this->fileRepository->delete($id);
    }
}