<?php

return [
    'generators' => [
        'validation' => [
            // The absolute base directory where files will be generated.
            'base_path' => app_path(),

            // The root PHP namespace for generated validation classes.
            'base_namespace' => 'App\\',

            // The sub-directory relative to base_path where classes are stored.
            'directory' => 'Validation',

            // The default class suffix.
            'type' => 'Validation',

            // Determine whether the type suffix should be appended to the generated class name (e.g., UserValidation).
            'force_type' => true,

            // Determine whether existing files should be overwritten without throwing an exception or requiring the --force flag.
            'overwrite' => false,
        ],
    ],

    // The mapping of validation rules to their specific rule type.
    'type_to_rule_map' => [
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

    // The fallback rule type to use when a rule cannot be resolved to a specific type.
    'default_rule_type' => 'constraint',

    // The execution priority assigned to each rule type.
    'type_to_priority_map' => [
        'modifier' => 1,
        'circuit' => 2,
        'presence' => 3,
        'type' => 4,
    ],

    // The fallback priority to use when a rule type has no explicit priority assigned.
    'default_rule_priority' => 100,
];
