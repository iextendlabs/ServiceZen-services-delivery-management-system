<?php

namespace App\Helpers;

use Intervention\Image\Facades\Image;

class ImageHelper
{
    public static function getOptimizedImageUrl($path, $width = null, $height = null)
    {
        $folder = dirname($path);
        $filename = basename($path);

        $params = [];
        if ($width) $params['w'] = $width;
        if ($height) $params['h'] = $height;

        // Automatically reduce quality for large images
        if (filesize(public_path($path)) > 100 * 1024) {
            $params['q'] = 75;
            $params['f'] = 'webp';
        }

        return route('image.render', [
            'folder' => $folder,
            'filename' => $filename
        ]) . (count($params) ? '?' . http_build_query($params) : '');
    }

    public static function getSrcSet($path, $sizes)
    {
        $set = [];
        foreach ($sizes as $width => $descriptor) {
            $set[] = self::getOptimizedImageUrl($path, $width) . " {$descriptor}";
        }
        return implode(', ', $set);
    }
}
