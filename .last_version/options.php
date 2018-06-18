<?php

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;

defined('ADMIN_MODULE_NAME') or define('ADMIN_MODULE_NAME', 'crtweb.cloudproperty');

global $USER, $APPLICATION;

if (!$USER->isAdmin()) {
    $APPLICATION->authForm('Nope');
}

$app = Application::getInstance();
$context = $app->getContext();
$request = $context->getRequest();

Loc::loadMessages($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/lang/ru/options.php');
Loc::loadMessages(__FILE__);

$tabControl = new CAdminTabControl('tabControl', [
    [
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('MAIN_TAB_SET'),
        'TITLE' => Loc::getMessage('MAIN_TAB_TITLE_SET'),
    ],
]);

if ((!empty($save) || !empty($restore)) && $request->isPost() && check_bitrix_sessid()) {
    if (!empty($restore)) {
        Option::delete(ADMIN_MODULE_NAME);
        CAdminMessage::showMessage([
            'MESSAGE' => Loc::getMessage('REFERENCES_OPTIONS_RESTORED'),
            'TYPE' => 'OK',
        ]);
    } else {
        $fields = [
            'clientId',
            'clientSecret',
            'authPath',
            'accessToken',
        ];
        foreach ($fields as $field) {
            if ($request->getPost($field) !== null) {
                Option::set(
                    ADMIN_MODULE_NAME,
                    $field,
                    $request->getPost($field)
                );
            }
        }
        if ($request->getPost('accessToken_del')) {
            Option::set(
                ADMIN_MODULE_NAME,
                'accessToken',
                ''
            );
        }
        
        CAdminMessage::showMessage([
            'MESSAGE' => Loc::getMessage('REFERENCES_OPTIONS_SAVED'),
            'TYPE' => 'OK',
        ]);
    }
}

$tabControl->begin();
?>
<form method="post"
      action="<?php echo sprintf('%s?mid=%s&lang=%s', $request->getRequestedPage(), urlencode($mid), LANGUAGE_ID); ?>">
    <?php
    echo bitrix_sessid_post();
    $tabControl->beginNextTab();
    ?>
    <tr>
        <td width="40%">
            <label><?php echo Loc::getMessage('MODULE_CLIENT_ID'); ?>:</label>
        <td width="60%">
            <input type="text"
                   size="50"
                   name="clientId"
                   value="<?php echo htmlentities(Option::get(ADMIN_MODULE_NAME, "clientId")); ?>"
            />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <label><?php echo Loc::getMessage('MODULE_CLIENT_SECRET'); ?>:</label>
        <td width="60%">
            <input type="text"
                   size="50"
                   name="clientSecret"
                   value="<?php echo htmlentities(Option::get(ADMIN_MODULE_NAME, "clientSecret")); ?>"
            />
        </td>
    </tr>
    <tr>
        <td width="40%">
            <label><?php echo Loc::getMessage('MODULE_LINK_AUTH'); ?>:</label>
        <td width="60%">
            <input type="text"
                   size="50"
                   name="authPath"
                   value="<?php echo htmlentities(Option::get(
        ADMIN_MODULE_NAME,
        "authPath",
                       '/bitrix/admin/dropbox_callback.php'
    )); ?>"
            />
        </td>
    </tr>
    <tr>
        <td width="40%">
            
            <label><?php echo Loc::getMessage('MODULE_TOKEN_TITLE'); ?>:</label>
        <td width="60%">
            <?php
            $token = Option::get(ADMIN_MODULE_NAME, "accessToken");
            if ($token):
                ?>
                <input type="text"
                       size="50"
                       name="accessToken"
                       value="<?php echo htmlentities(Option::get(ADMIN_MODULE_NAME, "accessToken")); ?>" disabled
                />
                <label>
                    <input type="checkbox" name="accessToken_del">
                    <?php echo Loc::getMessage('MODULE_TOKEN_DELETE_TOKEN'); ?>
                </label>
            <?php elseif ($path = Option::get(ADMIN_MODULE_NAME, "authPath")): ?>
                <a href="<?= $path ?>">
                    <?php echo Loc::getMessage('MODULE_TOKEN_GET_TOKEN'); ?>
                </a>
            <?php else: ?>
                <?php echo Loc::getMessage('MODULE_TOKEN_NEED_PAGE'); ?>
            <?php endif; ?>
        </td>
    </tr>
    <?php
    $tabControl->buttons();
    ?>
    <input type="submit"
           name="save"
           value="<?php echo Loc::getMessage('MAIN_SAVE'); ?>"
           title="<?php echo Loc::getMessage('MAIN_OPT_SAVE_TITLE'); ?>"
           class="adm-btn-save"
    />
    <input type="submit"
           name="restore"
           title="<?php echo Loc::getMessage('MAIN_HINT_RESTORE_DEFAULTS'); ?>"
           onclick="return confirm('<?php echo addslashes(GetMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING')); ?>')"
           value="<?php echo Loc::getMessage('MAIN_RESTORE_DEFAULTS'); ?>"
    />
    <?php
    $tabControl->end();
    ?>
</form>