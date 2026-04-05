<?php

namespace App\Services\Kyc;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentStorage
{
    protected $disk;
    protected $folder;

    public function __construct()
    {
        $config = config('kyc.storage');
        $this->disk = $config['disk'] ?? 'public';
        $this->folder = $config['folder'] ?? 'kyc-documents';
    }

    public function store(UploadedFile $file, $userId, $documentType)
    {
        // Generate a unique filename
        $filename = time() . '_' . $documentType . '.' . $file->getClientOriginalExtension();
        $path = $this->folder . '/' . $userId . '/' . $filename;

        // Store the file
        Storage::disk($this->disk)->put($path, file_get_contents($file->getRealPath()));

        // Return the public URL
        return Storage::disk($this->disk)->url($path);
    }

    public function delete($path)
    {
        return Storage::disk($this->disk)->delete($path);
    }
}
