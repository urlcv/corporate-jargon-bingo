<?php

declare(strict_types=1);

namespace URLCV\CorporateJargonBingo\Laravel;

use Illuminate\Support\ServiceProvider;

class CorporateJargonBingoServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'corporate-jargon-bingo');
    }
}
