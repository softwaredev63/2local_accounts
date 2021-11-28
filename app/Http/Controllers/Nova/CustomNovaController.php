<?php

namespace App\Http\Controllers\Nova;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class CustomNovaController
 * @package App\Http\Controllers
 */
class CustomNovaController extends Controller
{
    /**
     * Returns the nova user generated file or nothing.
     * @param Request $request
     * @return Application|ResponseFactory|Response|BinaryFileResponse
     */
    public function downloadFileNova(Request $request)
    {
        $fileName = $request->fileName;
        $filePath = $fileName ? "tmp/{$fileName}" : null;
        if($filePath && Storage::disk('public')->exists($filePath)) {
            return response()->download(Storage::path("public/{$filePath}"), $fileName)->deleteFileAfterSend(true);
        }
        return response("Nothing to see!", 404);
    }
}
