<?php

namespace App\Models;

use Illuminate\Contracts\Filesystem\Filesystem as FilesystemContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Sushi\Sushi;

class FileItem extends Model
{
    use Sushi;

    protected static string $disk;

    protected static string $path;

    protected array $schema = [
        'name' => 'string',
        'dateModified' => 'datetime',
        'size' => 'integer',
        'type' => 'string',
    ];

    public static function queryForDiskAndPath(string $disk = 'public', string $path = ''): Builder
    {
        static::$disk = $disk;
        static::$path = $path;

        return static::query();
    }

    public function isFolder(): bool
    {
        return $this->type === 'Folder'
            && is_dir(Storage::disk(static::$disk)->path($this->path));
    }

    public function isPreviousPath(): bool
    {
        return $this->name === '..';
    }

    public function delete(): bool
    {
        if ($this->isFolder()) {
            return Storage::disk(static::$disk)->deleteDirectory($this->path);
        }

        return Storage::disk(static::$disk)->delete($this->path);
    }

    public function canOpen(): bool
    {
        return $this->type !== 'Folder'
            && Storage::disk(static::$disk)->exists($this->path)
            && Storage::disk(static::$disk)->getVisibility($this->path) === FilesystemContract::VISIBILITY_PUBLIC;
    }

    public function getRows(): array
    {
        $backPath = [];
        if (self::$path) {
            $path = Str::of(self::$path)->explode('/');

            $backPath = [
                [
                    'name' => '..',
                    'dateModified' => null,
                    'size' => null,
                    'type' => 'Folder',
                    'path' => $path->count() > 1 ? $path->take($path->count() - 1)->join('/') : '',
                ],
            ];
        }

        $storage = Storage::disk(static::$disk);

        return collect($backPath)->push(
            ...collect($storage->directories(static::$path))
            ->sort()
            ->map(fn (string $directory): array => [
                'name' => Str::remove(self::$path.'/', $directory),
                'dateModified' => $storage->lastModified($directory),
                'size' => null,
                'type' => 'Folder',
                'path' => $directory,
            ]
            ),
            ...collect($storage->files(static::$path))
            ->sort()
            ->map(fn (string $file): array => [
                'name' => Str::remove(self::$path.'/', $file),
                'dateModified' => $storage->lastModified($file),
                'size' => $storage->size($file),
                'type' => $storage->mimeType($file) ?: null,
                'path' => $file,
            ]
            )
        )->toArray();
    }
}
