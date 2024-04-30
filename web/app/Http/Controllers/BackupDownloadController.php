<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class BackupDownloadController extends Controller
{
    public function download(Request $request)
    {
        if (!URL::signatureHasNotExpired($request)) {
            return response('The URL has expired.');
        }

        if (!URL::hasCorrectSignature($request)) {
            return response('Invalid URL provided');
        }

        return Storage::disk('backups')->download($request->get('path'));
    }
}

