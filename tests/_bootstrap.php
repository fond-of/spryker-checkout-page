<?php

use org\bovigo\vfs\vfsStream;
use Spryker\Shared\Config\Environment;

$pathToAutoloader = codecept_root_dir('vendor/autoload.php');

if (!file_exists($pathToAutoloader)) {
    $pathToAutoloader = codecept_root_dir('../../autoload.php');
}

require_once $pathToAutoloader;

if (!defined('APPLICATION_ENV')) {
    define('APPLICATION_ENV', Environment::TESTING);
}

if (!defined('APPLICATION_STORE')) {
    define('APPLICATION_STORE', 'UNIT');
}

if (!defined('APPLICATION_ROOT_DIR')) {
    $x = vfsStream::setup('root');
    define('APPLICATION_ROOT_DIR', $x->url());
}
