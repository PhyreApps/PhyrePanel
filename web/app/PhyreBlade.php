<?php

namespace App;

use Illuminate\Support\Facades\Blade;

class PhyreBlade
{
    public static function render($file, $data = [])
    {
        $namespace = '';
        if (strpos($file, '::') !== false) {
            [$namespace, $file] = explode('::', $file);
        }

        $file = str_replace('.', '/', $file);
        // Replace last / with .
        $file = preg_replace('/\/([^\/]*)$/', '.$1', $file);

        $hints = app()->view->getFinder()->getHints();
        if (isset($hints[$namespace])) {
            $path = $hints[$namespace][0] . '/' . $file;
        } else {
            $viewsPath = app()->view->getFinder()->getPaths()[0];
            $path = $viewsPath . '/' . $file;
        }

        if (!is_file($path)) {
            throw new \Exception('File not found: ' . $path);
        }

        $content = file_get_contents($path);

        $compiled = Blade::render($content, $data);

        return $compiled;

    }
}
