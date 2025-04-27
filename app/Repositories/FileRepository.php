<?php

namespace App\Repositories;

use App\Models\File;
use App\Repositories\Interfaces\FileRepositoryInterface;

class FileRepository implements FileRepositoryInterface
{
    public function all()
    {
        return File::all();
    }

    public function find($id)
    {
        return File::findOrFail($id);
    }

    public function create(array $data)
    {
        return File::create($data);
    }

    public function update($id, array $data)
    {
        $file = $this->find($id);
        $file->update($data);
        return $file;
    }

    public function delete($id)
    {
        $file = $this->find($id);
        $file->delete();
    }
}