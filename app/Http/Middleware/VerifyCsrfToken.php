<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'http://ofertasapp.es/*',
        'http://10.0.2.2/ofertasApp/public/*',
        'https://ofertasapp.es/*'
    ];
}
