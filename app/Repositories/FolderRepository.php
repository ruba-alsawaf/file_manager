<?php

namespace App\Repositories;

use App\Models\Folder;
use App\Repositories\Interfaces\FolderRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FolderRepository implements FolderRepositoryInterface
{
    public function all()
    {
        return Folder::all();
    }

    public function find($id): Folder
{
    $folder = Folder::find($id);
    if (!$folder) {
        throw new ModelNotFoundException("Folder not found");
    }
    return $folder;
}

    public function create(array $data): Folder
    {
        return Folder::create($data);
    }

    public function update($id, array $data): Folder
    {
        $folder = $this->find($id);
        $folder->update($data);
        return $folder;
    }
    

    public function delete($id): void
    {
        $folder = $this->find($id);
        $folder->delete();
    }
}