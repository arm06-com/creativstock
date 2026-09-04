<?php

echo 'user_ini_filename='.ini_get('user_ini.filename').PHP_EOL;
echo 'user_ini_cache_ttl='.ini_get('user_ini.cache_ttl').PHP_EOL;
echo 'user_ini_scan_dir='.ini_get('user_ini.scan_dir').PHP_EOL;
var_export($_SERVER['DOCUMENT_ROOT'] ?? 'no_document_root');
echo PHP_EOL;
