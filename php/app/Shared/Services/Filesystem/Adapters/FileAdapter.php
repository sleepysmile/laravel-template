<?php

namespace App\Shared\Services\Filesystem\Adapters;

use Illuminate\Http\UploadedFile;

class FileAdapter
{
    protected string $fileName;
    protected string $filePath;
    protected string $extension;
    protected ?string $mimeType;

    public function __construct(
        string $fileName,
        string $filePath,
        string $extension,
        string|null $mimeType,
    )
    {
        $this->fileName = $fileName;
        $this->filePath = $filePath;
        $this->extension = $extension;
        $this->mimeType = $mimeType;
    }

    public static function fromUploadedFile(UploadedFile $file)
    {
        return new self(
            $file->getClientOriginalName(),
            $file->path(),
            $file->extension(),
            $file->getMimeType(),
        );
    }

    public static function fromPath(string $path)
    {
        $pathInfo = pathinfo($path);

        return new self(
            $pathInfo["basename"],
            $path,
            $pathInfo["extension"],
            null
        );
    }

    public function extension()
    {
        return $this->extension;
    }

    public function content()
    {
        return file_get_contents($this->filePath);
    }
}
