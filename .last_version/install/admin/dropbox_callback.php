<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');

use Crtweb\CloudProperty\CloudService;
use Bitrix\Main\Loader;

Loader::includeModule("crtweb.cloudproperty");

$token = CloudService::getInstance()->auth();
if ($token) {
    LocalRedirect('/bitrix/admin/settings.php?mid=crtweb.cloudproperty&lang=ru');
}

CMain::FinalActions();
