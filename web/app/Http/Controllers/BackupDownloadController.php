<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\BackupStorage;
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

        $backupStorage = BackupStorage::getInstance($request->get('root_path'));
        return $backupStorage->download($request->get('path'));

    }
}

