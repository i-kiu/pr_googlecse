<?php

declare(strict_types=1);
$EM_CONF['pr_googlecse'] = [
    'title' => 'Google custom search engine',
    'description' => 'Integration of the Google custom search engine using Fluid templates for a customized look and feel.',
    'category' => 'plugin',
    'state' => 'stable',
    'author' => 'Pascal Rinker',
    'author_email' => 'info@kronova.net',
    'author_company' => 'kronova.net',
    'version' => '4.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '14.1.0-14.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
