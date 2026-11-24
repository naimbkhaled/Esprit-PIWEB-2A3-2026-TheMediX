<?php
// Redirect URI target for the OAuth providers: forwards to the single entry point.
$_GET['action'] = 'oauth_callback';
require __DIR__ . '/index.php';
