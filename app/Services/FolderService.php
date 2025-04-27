<?php

namespace App\Services;

use App\Repositories\Interfaces\FolderRepositoryInterface;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Models\Folder;

class FolderService
{
    public function __construct(
        private FolderRepositoryInterface $folderRepository
    ) {}

    public function createFolder(array $data): Folder
    {
        return DB::transaction(function () use ($data) {
            $parent = isset($data['parent_id']) ? $this->folderRepository->find($data['parent_id']) : null;

            $data['segment'] = $this->getNextSegment($parent);
            $data['path'] = $this->generatePath($parent, $data['segment']);

            return $this->folderRepository->create($data);
        });
    }

    private function getNextSegment(?Folder $parent): int
{
    return DB::transaction(function () use ($parent) {
        $query = $parent 
            ? Folder::where('parent_id', $parent->id)
            : Folder::whereNull('parent_id');

        $maxSegment = $query->lockForUpdate()->max('segment');
        return ($maxSegment ?? 0) + 1;
    });
}

    public function getAllFolders()
    {
        return $this->folderRepository->all();
    }

    public function getFolder($id)
    {
        return $this->folderRepository->find($id);
    }

    public function updateFolder($id, array $data): Folder
    {
        return DB::transaction(function () use ($id, $data) {
            $folder = $this->folderRepository->find($id);
            $oldPath = $folder->path;

            if (isset($data['parent_id']) && $data['parent_id'] != $folder->parent_id) {
                $newParent = $this->folderRepository->find($data['parent_id']);
                $this->validateParent($newParent, $folder);

                $data['segment'] = $this->getNextSegment($newParent);
                $data['path'] = $this->generatePath($newParent, $data['segment']);
            }

            $updatedFolder = $this->folderRepository->update($id, $data);
            $folder->refresh();

            if (isset($newParent)) {
                $this->updateDescendantsPaths($folder, $oldPath);
            }

            return $updatedFolder;
        });
    }

    private function updateDescendantsPaths(Folder $folder, string $oldPath): void
{
    $newPathPrefix = $folder->path . '.';
    $oldPathPrefix = $oldPath . '.';

    Folder::where('path', 'like', "{$oldPathPrefix}%")
        ->update([
            'path' => DB::raw("REPLACE(path, ?, ?)", [$oldPathPrefix, $newPathPrefix])
        ]);
}


    private function validateParent(?Folder $newParent, Folder $folder): void
    {
        if ($newParent && str_starts_with($newParent->path, $folder->path . '.')) {
            throw new \RuntimeException("Cannot move folder to its descendant");
        }
    }

    public function deleteFolder($id)
    {
        return $this->folderRepository->delete($id);
    }

    private function generatePath(?Folder $parent, int $segment): string
    {
        return $parent ? "{$parent->path}.{$segment}" : (string)$segment;
    }
    public function getFolderTree(): array
{
    return Folder::whereNull('parent_id')
        ->with(['childrenRecursive' => function ($query) {
            $query->with('childrenRecursive');
        }])
        ->get()
        ->map(function ($folder) {
            return $this->formatTree($folder);
        })->toArray();
}

    private function formatTree(Folder $folder): array
    {
        return [
            'id' => $folder->id,
            'name' => $folder->name,
            'path' => $folder->path,
            'children' => $folder->childrenRecursive->map(function ($child) {
                return $this->formatTree($child);
            })
        ];
    }
}
