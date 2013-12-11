<?php
define('VERSION', 0.1);
define('NL', PHP_EOL);
//define('DEFAULT_OUTPUT_DIR', __DIR__ . '/output'); //Absolute paths can be a pain on cygwin.
define('DEFAULT_OUTPUT_DIR', './output');
define('TEMPLATE_ASSET_PATH', './scripts/assets');

if (PHP_SAPI !== 'cli') {
    trigger_error('Script can only run from CLI!', E_USER_ERROR);
    die;
}

require_once "CLI.class.php";
require_once "Manifest.class.php";
require_once "Parser.class.php";
require_once "DevBoxHelper.class.php";

require_once "Puppetmodules.class.php";


