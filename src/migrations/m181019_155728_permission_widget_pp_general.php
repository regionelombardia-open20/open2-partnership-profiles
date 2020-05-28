<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    Open20Package
 * @category   CategoryName
 */
use open20\amos\core\migration\AmosMigrationPermissions;
use yii\rbac\Permission;


/**
* Class m180327_162827_add_auth_item_een_archived*/
class m181019_155728_permission_widget_pp_general extends AmosMigrationPermissions
{

    /**
    * @inheritdoc
    */
    protected function setRBACConfigurations()
    {
        $prefixStr = 'Permissions for the dashboard for the widget ';

        return [
            [
                'name' =>  \open20\amos\partnershipprofiles\widgets\icons\WidgetIconPartnershipProfilesDashboardGeneral::className(),
                'type' => Permission::TYPE_PERMISSION,
                'description' => $prefixStr . 'WidgetIconPartnershipProfiles',
                'ruleName' => null,
                'parent' => ['PARTNERSHIP_PROFILES_READER','PARTNERSHIP_PROFILES_ADMINISTRATOR','PARTNERSHIP_PROFILES_CREATOR', 'PARTNERSHIP_PROFILES_VALIDATOR']
           ]
        ];
    }
}
