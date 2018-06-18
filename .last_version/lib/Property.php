<?php

namespace Crtweb\CloudProperty;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Crtweb\CloudProperty\File\File;

Loc::loadMessages(__FILE__);
Loader::includeModule("crtweb.cloudproperty");

class Property
{
    public function getDescription()
    {
        return [
            "PROPERTY_TYPE" => "S",
            "USER_TYPE" => "Cloud",
            "DESCRIPTION" => Loc::getMessage('CRTWEB_PROPERTY_DESCRIPTION'),
            "PrepareSettings" => ["\Crtweb\CloudProperty\Property", "prepareSettings"],
            "GetSettingsHTML" => ["\Crtweb\CloudProperty\Property", "getSettingsHTML"],
            "GetPropertyFieldHtml" => ["\Crtweb\CloudProperty\Property", "getPropertyFieldHtml"],
            "GetPropertyFieldHtmlMulty" => ["\Crtweb\CloudProperty\Property", "getPropertyFieldHtmlMulty"],
            "ConvertToDB" => ["\Crtweb\CloudProperty\Property", "convertToDB"],
            "GetAdminListViewHTML" => ["\Crtweb\CloudProperty\Property", "getAdminListViewHTML"],
            "GetPublicViewHTML" => ["\Crtweb\CloudProperty\Property", "getPublicViewHTML"],
        ];
    }
    
    public function convertToDB($arProperty, $value)
    {
        if (
            $value['VALUE']['DEL'] == "Y" &&
            isset($value['VALUE']['CURRENT']) &&
            CloudService::getInstance()->check($value['VALUE']['CURRENT'])
        ) {
            CloudService::getInstance()->delete($value['VALUE']['CURRENT']);
        } elseif (is_array($value['VALUE']['FILE'])) {
            if (isset($value['VALUE']['CURRENT']) && CloudService::getInstance()->check($value['VALUE']['CURRENT'])) {
                CloudService::getInstance()->delete($value['VALUE']['CURRENT']);
            }
            $file = new File($value['VALUE']['FILE']);
            $value["VALUE"] = CloudService::getInstance()->save($file);
        }
        
        return $value;
    }
    
    public function getPropertyFieldHtmlMulty($arProperty, $value, $strHTMLControlName)
    {
        return self::makeInput($arProperty, $value, $strHTMLControlName);
    }
    
    private static function makeInput($arProperty, $value, $strHTMLControlName)
    {
        $accessToken = Option::get('crtweb.cloudproperty', 'accessToken');
        if (empty($accessToken)) {
            $html = '<a href="/bitrix/admin/settings.php?mid=crtweb.cloudproperty&lang=ru">' . Loc::getMessage('CRTWEB_PROPERTY_NEED_TOKEN') . '</a>';
        } else {
            $link = false;
            if ($value['VALUE'] && $value['VALUE'] != '1' && CloudService::getInstance()->check($value['VALUE'])) {
                $link = CloudService::getInstance()->get($value['VALUE']);
            }
            
            if ($link) {
                $message_edit = Loc::getMessage('CRTWEB_PROPERTY_FILE_EDIT');
                $message_delete = Loc::getMessage('CRTWEB_PROPERTY_FILE_DELETE');
                $html = "<a href='{$link}'>{$value['VALUE']}</a>
                <p>
                    <span class='adm-input-file'>
                        <span>{$message_edit}</span>
                        <input type='file' name='{$strHTMLControlName["VALUE"]}[FILE]' class='adm-designed-file'>
                    </span>
                </p>
                <p>
                    <label>
                        <input type='hidden' name='{$strHTMLControlName["VALUE"]}[CURRENT]' value='{$value["VALUE"]}'>
                        <input type='checkbox' name='" . $strHTMLControlName["VALUE"] . "[DEL]' value='Y'>
                        {$message_delete}
                    </label>
                </p>";
            } else {
                $message_add = Loc::getMessage('CRTWEB_PROPERTY_FILE_ADD');
                $html = "<span class='adm-input-file'>
                    <span>{$message_add}</span>
                    <input type='file' name='{$strHTMLControlName["VALUE"]}[FILE]' value='{$value["VALUE"]}' class='adm-designed-file'>
                  </span>";
            }
        }
        return $html;
    }
    
    public function getPropertyFieldHtml($arProperty, $value, $strHTMLControlName)
    {
        return self::makeInput($arProperty, $value, $strHTMLControlName);
    }
    
    public function prepareSettings($arFields)
    {
        return $arFields["USER_TYPE_SETTINGS"];
    }

    public function getPublicViewHTML($arProperty, $value){
        if (CloudService::getInstance()->check($value['VALUE'])) {
            $link = CloudService::getInstance()->get($value['VALUE']);
            return "<a href=\"{$link}\" download>{$value['VALUE']}</a>";
        } else {
            return '';
        }
    }
}
