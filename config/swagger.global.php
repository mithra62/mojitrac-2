<?php

return array(
    'swagger' => array(
        /**
         * List a path or paths containing Swagger Annotated classes
         */
        'paths' => array(
            __DIR__ . '/../module/Api/src/Api/Controller',
        ),

        'resource_options' => array(
            'output' => 'array',
            'json_pretty_print' => true, // for outputtype 'json'
            'defaultBasePath' => null,
            'defaultApiVersion' => null,
            'defaultSwaggerVersion' => '1.2',
        ),
    )
);
