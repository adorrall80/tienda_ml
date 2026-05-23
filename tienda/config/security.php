<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Palabras y patrones bloqueados en campos de texto
    |--------------------------------------------------------------------------
    |
    | Lista mantenible para bloquear entradas con comandos o patrones que suelen
    | aparecer en intentos de SQL injection, XSS o abuso de formularios.
    |
    */
    'blocked_text_terms' => [
        'select',
        'from',
        'where',
        'union',
        'insert',
        'update',
        'delete',
        'drop',
        'truncate',
        'alter',
        'create',
        'replace',
        'exec',
        'execute',
        'declare',
        'sleep',
        'benchmark',
        'script',
        'iframe',
        'object',
        'embed',
        'onerror',
        'onload',
        'onclick',
        'javascript:',
        'data:text/html',
        '<script',
        '</script',
    ],
];
