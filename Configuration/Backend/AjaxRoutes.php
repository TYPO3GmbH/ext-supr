<?php

/**
 * Backend AJAX routes for EXT:supr
 */
return [
    'supr_render_widget' => [
        'path' => '/supr/widget',
        'target' => \Supr\Supr\Controller\AjaxController::class . '::renderAction',
    ],
];