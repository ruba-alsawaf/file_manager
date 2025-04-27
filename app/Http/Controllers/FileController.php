<?php

namespace App\Http\Controllers;

use App\Http\Requests\FileCreateRequest;
use App\Http\Requests\FileUpdateRequest;
use App\Services\FileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function __construct(
        private FileService $fileService
    ) {}

    public function create(FileCreateRequest $request): JsonResponse
    {
        try {
            $file = $this->fileService->uploadFile(
                $request->file('file'),
                $request->folder_id
            );
            
            return response()->json([
                'message' => 'File uploaded successfully',
                'data' => $file
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'File upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    
    public function show($id): JsonResponse
    {
        try {
            $file = $this->fileService->getFile($id);
            return response()->json([
                'data' => [
                    'file' => $file,
                    'download_url' => Storage::url($file->path)
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'File not found'
            ], 404);
        }
    }

    
    public function update(FileUpdateRequest $request, $id): JsonResponse
    {
        try {
            $file = $this->fileService->updateFile($id, $request->validated());
            return response()->json([
                'message' => 'File updated successfully',
                'data' => $file
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'File update failed: ' . $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->fileService->deleteFile($id);
            return response()->json([
                'message' => 'File deleted successfully'
            ], 204);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'File deletion failed: ' . $e->getMessage()
            ], 400);
        }
    }

    
    public function download($id): JsonResponse
    {
        try {
            $file = $this->fileService->getFile($id);
            
            if (!Storage::exists($file->path)) {
                throw new \Exception('File not found on server');
            }
            
            return response()->json([
                'download_url' => Storage::url($file->path),
                'direct_download' => route('files.download', $id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Download failed: ' . $e->getMessage()
            ], 404);
        }
    }
}