<?php

namespace App;

class FileManagerApi
{
    public function isDir($path)
    {
        return is_dir($path);
    }

    public function isFile($path)
    {
        return is_file($path);
    }

    public function isWritable($path)
    {
        return is_writable($path);
    }

    public function mkdir($path)
    {
        return mkdir($path, 0755, true);
    }

    public function fileGetContents($file)
    {
        return file_get_contents($file);
    }

    public function symlink($source, $destination)
    {
        return symlink($source, $destination);
    }

    public function fileExists($file)
    {
        return file_exists($file);
    }

    public function filePutContents($file, $data)
    {
        return file_put_contents($file, $data);
    }
}
