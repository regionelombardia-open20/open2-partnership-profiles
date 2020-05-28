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
use open20\amos\partnershipprofiles\models\ExpressionsOfInterest;
use open20\amos\partnershipprofiles\models\search\ExpressionsOfInterestSearch;
use open20\amos\partnershipprofiles\Module;
use Yii;

/**
 * Class WidgetIconExpressionsOfInterestReceived
 * @package open20\amos\partnershipprofiles\widgets\icons
 */
class WidgetIconExpressionsOfInterestReceived extends WidgetIcon
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

        $this->setLabel(Module::tHtml('amospartnershipprofiles', 'Received'));
        $this->setDescription(Module::t('amospartnershipprofiles', 'Show the expressions of interest received'));

        if (!empty(Yii::$app->params['dashboardEngine']) && Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('propostecollaborazione');
            $paramsClassSpan = [];
        } else {
            $this->setIcon('partnership-profiles');
        }

        $this->setUrl(['/partnershipprofiles/expressions-of-interest/received']);
        $this->setCode('EXPRESSIONS_OF_INTEREST_RECEIVED');
        $this->setModuleName('partnershipprofiles');
        $this->setNamespace(__CLASS__);

        $loggedUser = \Yii::$app->user->identity;
        $search = new ExpressionsOfInterestSearch();
        $query = $search->searchReceivedQuery([]);
        $query->andWhere([
                '>=',
                ExpressionsOfInterest::tableName() . '.created_at',
                $loggedUser->userProfile->ultimo_logout]
        );

        $this->setBulletCount(
            $this->makeBulletCounter(
                Yii::$app->getUser()->getId(),
                ExpressionsOfInterest::className(),
                $search->searchReceivedQuery([])
            )
        );
    }
}
