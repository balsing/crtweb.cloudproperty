<?php

namespace Crtweb\CloudProperty;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Crtweb\CloudProperty\File\File;

Loc::loadMessages(__FILE__);
Loader::includeModule("crtweb.cloudproperty");

class UserProperty
{
    public function getUserTypeDescription()
    {
        return [
            'USER_TYPE_ID' => 'cloudproperty',
            'CLASS_NAME' => __CLASS__,
            'DESCRIPTION' => Loc::getMessage('CRTWEB_PROPERTY_DESCRIPTION'),
            'BASE_TYPE' => 'string'
        ];
    }
    
    public function OnBeforeSave($arUserField, $value)
    {
        if (
            $value['DEL'] == "Y" &&
            isset($value['CURRENT']) &&
            CloudService::getInstance()->check($value['CURRENT'])
        ) {
            CloudService::getInstance()->delete($value['CURRENT']);
        } elseif (is_array($value['FILE'])) {
            if (isset($value['CURRENT']) && CloudService::getInstance()->check($value['CURRENT'])) {
                CloudService::getInstance()->delete($value['CURRENT']);
            }
            $file = new File($value['FILE']);
            $value = CloudService::getInstance()->save($file);
        }
        return $value;
    }
    
    /**
     * Возвращает форму для настройки поля в административной части.
     *
     * @param bool|array $arUserField
     * @param array      $arHtmlControl
     * @param bool       $bVarsFromForm
     *
     * @return string
     */
    public function GetSettingsHTML($arUserField = false, $arHtmlControl, $bVarsFromForm)
    {
        return '';
    }
    /**
     * Метод возвращает массив с дополнительными настройками свойства.
     *
     * @param array $arFields
     *
     * @return array
     */
    public function PrepareSettings($arUserField)
    {
        return [];
    }
    
    /**
     * Возвращает html для поля для ввода, которое отбразится в административной части.
     *
     * @param array $field   Свойства поля из настроек административной части
     * @param array $control Массив с именами для элементов поля из битрикса
     *
     * @return string
     */
    public function GetEditFormHTML($field, $value)
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
                        <input type='file' name='{$field["FIELD_NAME"]}[FILE]' class='adm-designed-file'>
                    </span>
                </p>
                <p>
                    <label>
                        <input type='hidden' name='{$field["FIELD_NAME"]}[CURRENT]' value='{$value["VALUE"]}'>
                        <input type='checkbox' name='" . $field["FIELD_NAME"] . "[DEL]' value='Y'>
                        {$message_delete}
                    </label>
                </p>";
            } else {
                $message_add = Loc::getMessage('CRTWEB_PROPERTY_FILE_ADD');
                $html = "<span class='adm-input-file'>
                    <span>{$message_add}</span>
                    <input type='file' name='{$field["FIELD_NAME"]}[FILE]' value='{$value["VALUE"]}' class='adm-designed-file'>
                  </span>";
            }
        }
        return $html;
    }
    
    /**
     * Возвращает описание колонки в базе данных, которая будет создана для сущности.
     */
    public function GetDBColumnType($field)
    {
        return 'text';
    }
}
