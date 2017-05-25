<?php
use Whoops\Handler\PrettyPageHandler;
use Zend\Expressive\Container\WhoopsErrorHandlerFactory;

return [
    'dependencies' => [
        'invokables' => [
            'Zend\Expressive\Whoops' => Whoops\Run::class,
            'Zend\Expressive\WhoopsPageHandler' => PrettyPageHandler::class,
        ],
        'factories' => [
            'Zend\Expressive\FinalHandler' => WhoopsErrorHandlerFactory::class,
        ],
    ],

    'whoops' => [
        'json_exceptions' => [
            'display'    => true,
            'show_trace' => true,
            'ajax_only'  => true,
        ],
    ],
];
