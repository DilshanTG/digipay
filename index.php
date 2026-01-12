<?php
/**
 * cPanel Deployment Helper
 * Redirects root access to the public folder
 * 
 * Usage: Upload entire project to cPanel, this file handles the redirect
 */

// Transparently include the public index
require __DIR__ . '/public/index.php';
exit;
