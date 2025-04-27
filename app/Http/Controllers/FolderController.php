<?php

namespace App\Http\Controllers;

use App\Http\Requests\FolderCreateRequest;
use App\Http\Requests\FolderUpdateRequest;
use App\Services\FolderService;
use Illuminate\Http\JsonResponse;

class FolderController extends Controller
{
    public function __construct(
        private FolderService $folderService
    ) {}

    public function index(): JsonResponse
    {
        $folders = $this->folderService->getAllFolders();
        return response()->json($folders);
    }

    public function create(FolderCreateRequest $request): JsonResponse
    {
        try {
            $folder = $this->folderService->createFolder($request->validated());
            return response()->json($folder, 201);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $folder = $this->folderService->getFolder($id);
            return response()->json($folder);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Folder not found'], 404);
        }
    }

    public function update(FolderUpdateRequest $request, $id): JsonResponse
    {
        try {
            $folder = $this->folderService->updateFolder($id, $request->validated());
            return response()->json($folder);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->folderService->deleteFolder($id);
            return response()->json(null, 204);
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function tree(): JsonResponse
    {
        $tree = $this->folderService->getFolderTree();
        return response()->json($tree);
    }
}