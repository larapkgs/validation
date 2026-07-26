<?php

return [
    'generators' => [
        'validation' => [
            'base_path' => app_path(),
            'base_namespace' => 'App\\',
            'directory' => 'Validation',
        ],
    ],
    'typeToRuleMap' => [
        'modifier' => [
            'sometimes', 'nullable', 'exclude', 'exclude_if', 'exclude_unless', 'exclude_with', 'exclude_without',
        ],
        'circuit' => [
            'bail',
        ],
        'presence' => [
            'required', 'required_array_keys', 'required_if', 'required_if_accepted', 'required_if_declined', 'required_unless', 'required_with',
            'required_with_all', 'required_without', 'required_without_all',
            'prohibited', 'prohibited_if', 'prohibited_if_accepted', 'prohibited_if_declined', 'prohibited_unless', 'prohibits',
            'missing', 'missing_if', 'missing_unless', 'missing_with', 'missing_with_all',
            'present', 'present_if', 'present_unless', 'present_with', 'present_with_all',
            'filled', 'accepted', 'accepted_if', 'declined', 'declined_if',
        ],
        'type' => [
            'string', 'array', 'integer', 'numeric', 'boolean', 'file',
        ],
    ],
    'typeToPriorityMap' => [
        'modifier' => 1,
        'circuit' => 2,
        'presence' => 3,
        'type' => 4,
    ],
];
