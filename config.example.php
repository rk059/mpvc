<?php
// Copy this file to config.php on cPanel and replace the placeholder values.
// Keep config.php out of version control because it contains credentials.
return [
    'db_host' => 'localhost',
    'db_name' => 'cpanel_database_name',
    'db_user' => 'cpanel_database_user',
    'db_password' => 'database_password',
    'enquiry_email' => 'your-email@example.com',
    // Optional: set these on cPanel to provision the first admin account automatically.
    'admin_name' => 'Website Admin',
    'admin_email' => 'admin@example.com',
    'admin_password' => 'replace-with-a-strong-password',
];
