<?

// backward compatibility
if (!class_exists('\PHPUnit\Framework\TestCase') &&
    class_exists('\PHPUnit_Framework_TestCase')) {
    class_alias('\PHPUnit_Framework_TestCase', '\PHPUnit\Framework\TestCase');
}

function loader($class)
{
    $file = $class . '.php';
    if (file_exists($file)) {
        require $file;
    }
}
spl_autoload_register('loader');

require dirname(__DIR__) . '/vendor/autoload.php';
require __DIR__ . '/stubs/Application_stub.php';
require __DIR__ . '/stubs/Connector_stub.php';
require __DIR__ . '/stubs/Dropbox_app_stub.php';
require __DIR__ . '/stubs/Dropbox_file_stub.php';
require __DIR__ . '/stubs/Dropbox_stub.php';
require __DIR__ . '/stubs/Option_stub.php';

