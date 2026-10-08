<?php

namespace App\Support;

use Illuminate\Foundation\Vite;
use Throwable;

class SafeVite extends Vite
{
    /**
     * Resolve an asset from Vite manifest, falling back to standard asset URL if missing.
     *
     * @param  string  $asset
     * @param  string|null  $buildDirectory
     * @return string
     */
    public function asset($asset, $buildDirectory = null)
    {
        try {
            return parent::asset($asset, $buildDirectory);
        } catch (Throwable $e) {
            return asset($asset);
        }
    }
}
