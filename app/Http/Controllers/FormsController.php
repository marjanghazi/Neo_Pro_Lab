<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FormsController extends Controller
{
    public function download($filename)
    {
        $path = 'public/files/' . $filename;

        if (!Storage::exists($path)) {
            abort(404);
        }

        return Storage::download($path);
    }
}
