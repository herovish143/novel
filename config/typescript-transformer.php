<?php

return [
    'searching_paths' => [
        app_path(),
        base_path('domain'),
    ],

    'transformers' => [
        Spatie\TypeScriptTransformer\Transformers\AttributedClassTransformer::class,
        Spatie\TypeScriptTransformer\Transformers\EnumTransformer::class,
    ],

    'output_file' => resource_path('js/types/generated.d.ts'),

    'writer' => Spatie\TypeScriptTransformer\Writers\SingleFileWriter::class,

    'formatter' => Spatie\TypeScriptTransformer\Formatters\PrettierFormatter::class,

    'transform_to_native_enums' => false,
];
