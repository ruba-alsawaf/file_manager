<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FoldersFilesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $root = \App\Models\Folder::create([
            'name' => 'Root',
            'path' => '1'
        ]);
    
        $subFolder = \App\Models\Folder::create([
            'name' => 'Documents',
            'parent_id' => $root->id,
            'path' => '1.1'
        ]);
    
        $subFolder = \App\Models\Folder::create([
            'name' => 'Documents1',
            'parent_id' => $root->id,
            'path' => '1.1.1'
        ]);

        $subFolder = \App\Models\Folder::create([
            'name' => 'Documents2',
            'parent_id' => $root->id,
            'path' => '1.1.2'
        ]);

        $subFolder = \App\Models\Folder::create([
            'name' => 'Documents3',
            'parent_id' => $root->id,
            'path' => '1.2'
        ]);

        $subFolder = \App\Models\Folder::create([
            'name' => 'Documents4',
            'parent_id' => $root->id,
            'path' => '1.2.1'
        ]);

        \App\Models\File::create([
            'name' => 'example.pdf',
            'path' => 'files/1/example.pdf',
            'folder_id' => $root->id,
        ]);
    }
}
