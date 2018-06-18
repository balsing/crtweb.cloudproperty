<?php

use Bitrix\Main\Application;
use Bitrix\Main\EventManager;
use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

class crtweb_cloudproperty extends CModule
{
    public $MODULE_ID = 'crtweb.cloudproperty';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_NAME;
    public $PARTNER_URI;
    
    public function __construct()
    {
        if (file_exists(__DIR__ . "/version.php")) {
            $arModuleVersion = require_once(__DIR__ . "/version.php");
            $this->MODULE_VERSION = $arModuleVersion["VERSION"];
            $this->MODULE_VERSION_DATE = $arModuleVersion["VERSION_DATE"];
            $this->MODULE_NAME = Loc::getMessage('CRTWEB_MOBULE_NAME');
            $this->MODULE_DESCRIPTION = Loc::getMessage('CRTWEB_MOBULE_DESCRIPTION');
            $this->PARTNER_NAME = Loc::getMessage('CRTWEB_PARTNER_NAME');
            $this->PARTNER_URI = Loc::getMessage('CRTWEB_PARTNER_URI');
        }
    }
    
    public function DoInstall()
    {
        $this->installEvents();
        $this->installFiles();
        RegisterModule($this->MODULE_ID);
    }
    
    public function DoUnInstall()
    {
        $this->uninstallEvents();
        $this->unInstallFiles();
        UnRegisterModule($this->MODULE_ID);
    }
    
    public function installEvents()
    {
        $eventManager = EventManager::getInstance();
        foreach ($this->getEventsList() as $event) {
            $eventManager->registerEventHandlerCompatible(
                $event['FROM_MODULE_ID'],
                $event['EVENT_TYPE'],
                $this->MODULE_ID,
                $event['TO_CLASS'],
                $event['TO_METHOD'],
                $event['SORT']
            );
        }
    }
    
    public function uninstallEvents()
    {
        $eventManager = EventManager::getInstance();
        foreach ($this->getEventsList() as $event) {
            $eventManager->unRegisterEventHandler(
                $event['FROM_MODULE_ID'],
                $event['EVENT_TYPE'],
                $this->MODULE_ID,
                $event['TO_CLASS'],
                $event['TO_METHOD']
            );
        }
    }
    
    /**
     * Возвращает список событий, которые должны быть установлены для данного модуля.
     *
     * @return array
     */
    protected function getEventsList()
    {
        return [
            [
                'FROM_MODULE_ID' => 'main',
                'EVENT_TYPE' => 'OnUserTypeBuildList',
                'TO_CLASS' => "\Crtweb\CloudProperty\UserProperty",
                'TO_METHOD' => "getUserTypeDescription",
                'SORT' => '1800',
            ],
            [
                'FROM_MODULE_ID' => 'iblock',
                'EVENT_TYPE' => 'OnIBlockPropertyBuildList',
                'TO_CLASS' => "\Crtweb\CloudProperty\Property",
                'TO_METHOD' => "getDescription",
                'SORT' => '1800',
            ],
        ];
    }
    
    
    public function installFiles()
    {
        CopyDirFiles(
            __DIR__ . '/admin/',
            Application::getDocumentRoot() . '/bitrix/admin/'
        );
    }
    
    public function unInstallFiles()
    {
        DeleteDirFiles(
            __DIR__ . '/admin/',
            Application::getDocumentRoot() . '/bitrix/admin/'
        );
    }
}
