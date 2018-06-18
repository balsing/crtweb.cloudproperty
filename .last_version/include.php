<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Event;
use Crtweb\CloudProperty\CloudService;
use Crtweb\CloudProperty\Connectors\DropboxConnector;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;

require __DIR__ . '/lib/external/autoload.php';

\Bitrix\Main\Loader::registerAutoLoadClasses(
    "crtweb.cloudproperty",
    [
        'Crtweb\CloudProperty\Connectors\ConnectorInterface' => 'lib/Connectors/ConnectorInterface.php',
        'Crtweb\CloudProperty\Connectors\DropboxConnector' => 'lib/Connectors/DropboxConnector.php',
        'Crtweb\CloudProperty\File\File' => 'lib/File/File.php',
        'Crtweb\CloudProperty\CloudService' => 'lib/CloudService.php',
        'Crtweb\CloudProperty\Property' => 'lib/Property.php',
        'Crtweb\CloudProperty\UserProperty' => 'lib/UserProperty.php',
    ]
);

//событие для того, чтобы другие модули могли подключить свой конектор
$event = new Event('crtweb.cloud_property', 'createCloudProperty');
$event->send();

//если в событии не подключен логер, то инстантим по умолчанию
if (!$connector = $event->getParameter('connector')) {
    $clientId = Option::get('crtweb.cloudproperty', 'clientId');
    $clientSecret = Option::get('crtweb.cloudproperty', 'clientSecret');
    $accessToken = Option::get('crtweb.cloudproperty', 'accessToken');
    
    $app = new DropboxApp($clientId, $clientSecret, $accessToken);
    $dropbox = new Dropbox($app);
    $connector = new DropboxConnector($app, $dropbox);
}

CloudService::getInstance()->setConnector($connector);
