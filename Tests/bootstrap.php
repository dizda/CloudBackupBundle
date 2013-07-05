<?php

/*$file = __DIR__.'/../../../../vendor/autoload.php';

if(!file_exists($file))
{
    throw new RuntimeException('Install dependencies to run test suite. "php composer.phar install --dev"');
}*/

// installed via composer?
if (file_exists($a = __DIR__.'/../../../../vendor/autoload.php')) {
    require_once $a;
} else {
    require_once __DIR__.'/../vendor/autoload.php';
}

//require_once $file;