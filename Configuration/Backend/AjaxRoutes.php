<?php

/**
 * Backend AJAX routes for EXT:supr
 */
return [
    'supr_render_widget' => [
        'path' => '/supr/widget',
        'target' => \WMDB\Supr\Controller\AjaxController::class . '::renderAction',
    ],
];