<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function delete(string $id): \Illuminate\Http\JsonResponse
    {
        $file = File::findOrFail($id);
        $relativePath = str_replace('/storage', '', $file->src);
        $exists = Storage::disk('public')->exists($relativePath);
        if($exists){
            Storage::disk('public')->delete($relativePath);
        }
        $file->delete();
        return response()->json(['message' => 'true']);
    }
}
