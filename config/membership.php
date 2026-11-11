<?php

return [
    'authority_name' => env('MEMBERSHIP_AUTHORITY_NAME', ''),
    'authority_title' => env('MEMBERSHIP_AUTHORITY_TITLE', 'Kepala Perpustakaan'),
    'verification_base_url' => env('MEMBERSHIP_VERIFICATION_BASE_URL'),
    'lan_url_file' => base_path('.runtime/active-lan-url'),
];
