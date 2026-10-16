<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Comma-separated IPs or CIDR ranges of the reverse proxy (Traefik) whose
    | X-Forwarded-* headers may be trusted. Never "*": a forged
    | X-Forwarded-Host could poison signed URLs and password reset links
    | (docs/SEGURIDAD.md, Web_Cache_Security). Unset means no proxy is trusted.
    |
    */

    'proxies' => env('TRUSTED_PROXIES'),

];
