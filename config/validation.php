<?php

return [
    'generators' => [
        'validation' => [
            'base_path' => app_path(),
            'base_namespace' => 'App\\',
            'directory' => 'Validation',
        ]
    ],
    'typeToRuleMap' => [
        'modifier' => [
            'sometimes', 'nullable', 'exclude', 'exclude_if', 'exclude_unless',
            'exclude_with', 'exclude_without'
        ],
        'circuit' => [
            'bail'
        ],
        'presence' => [
            'required', 'required_if', 'required_unless', 'required_with',
            'required_with_all', 'required_without', 'required_without_all',
            'prohibited', 'prohibited_if', 'prohibited_unless', 'missing',
            'missing_if', 'missing_unless', 'filled', 'accepted', 'accepted_if',
            'declined', 'declined_if'
        ],
        'type' => [
            'string', 'array', 'integer', 'numeric', 'boolean', 'json',
            'file', 'image', 'object', 'list'
        ],
    ]
];