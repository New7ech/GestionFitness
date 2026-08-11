<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    public function show(string $path): StreamedResponse|Response
    {
        $normalizedPath = ltrim(trim($path), '/');

        if (
            $normalizedPath === ''
            || str_contains($normalizedPath, '..')
            || str_contains($normalizedPath, '\\')
        ) {
            abort(404);
        }

        if (! Storage::disk('public')->exists($normalizedPath)) {
            abort(404);
        }

        return Storage::disk('public')->response($normalizedPath);
    }
}
