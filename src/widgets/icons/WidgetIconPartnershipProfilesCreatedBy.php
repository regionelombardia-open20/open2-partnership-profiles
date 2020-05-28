<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\partnershipprofiles\widgets\icons
 * @category   CategoryName
 */

namespace open20\amos\partnershipprofiles\widgets\icons;

use open20\amos\core\icons\AmosIcons;
use open20\amos\core\widget\WidgetAbstract;
use open20\amos\core\widget\WidgetIcon;
use open20\amos\partnershipprofiles\models\PartnershipProfiles;
use open20\amos\partnershipprofiles\models\search\PartnershipProfilesSearch;
use open20\amos\partnershipprofiles\Module;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconPartnershipProfilesCreatedBy
 * @package open20\amos\partnershipprofiles\widgets\icons
 */
class WidgetIconPartnershipProfilesCreatedBy extends WidgetIcon
{

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $paramsClassSpan = [
            'bk-backgroundIcon',
            'color-primary'
        ];

        $this->setLabel(Module::tHtml('amospartnershipprofiles', '#created_by_me_partnershippprofiles'));
        $this->setDescription(Module::t('amospartnershipprofiles', 'Show the partnership profiles created by me'));

        if (!empty(Yii::$app->params['dashboardEngine']) && Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('propostecollaborazione');
            $paramsClassSpan = [];
        } else {
            $this->setIcon('partnership-profiles');
        }

        $this->setUrl(['/partnershipprofiles/partnership-profiles/created-by']);
        $this->setCode('PARTNERSHIP_PROFILES_CREATEDBY');
        $this->setModuleName('partnershipprofiles');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                $paramsClassSpan
            )
        );

//        $search = new PartnershipProfilesSearch();
//        $this->setBulletCount(
//            $this->makeBulletCounter(
//                Yii::$app->getUser()->getId(),
//                PartnershipProfiles::className(),
//                $search->searchCreatedByQuery([])
//            )
//        );
    }

}
