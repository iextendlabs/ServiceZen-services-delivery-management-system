<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class JsonCacheHelper
{
    /**
     * Delete JSON cache files for a given slug and path.
     *
     * @param string $slug
     * @param string $basePath
     * @return void
     */
    public static function deleteJsonCacheFiles($slug, $basePath)
    {
        $itemBase = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $patterns = [
            $itemBase . $slug . '.json',
            $itemBase . $slug . '_*.json',
        ];
        foreach ($patterns as $pattern) {
            foreach (glob($pattern) as $file) {
                if (@unlink($file)) {
                    Log::info("Deleted JSON cache file: $file");
                } else {
                    Log::error("Failed to delete JSON cache file: $file");
                }
            }
        }
    }
}