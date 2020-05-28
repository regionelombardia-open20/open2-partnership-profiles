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

use open20\amos\core\widget\WidgetIcon;
use open20\amos\partnershipprofiles\Module;
use open20\amos\core\widget\WidgetAbstract;
use open20\amos\core\icons\AmosIcons;

use yii\helpers\ArrayHelper;

/**
 * Class WidgetIconPartnerProfExprOfIntExprOfInt
 * @package open20\amos\partnershipprofiles\widgets\icons
 */
class WidgetIconPartnerProfExprOfIntExprOfInt extends WidgetIcon {

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        $paramsClassSpan = [
            'bk-backgroundIcon',
            'color-primary'
        ];

        $this->setLabel(Module::tHtml('amospartnershipprofiles', 'Expressions Of Interest'));
        $this->setDescription(Module::t('amospartnershipprofiles', 'Expressions of interest of the user you facilitate'));

        if (!empty(\Yii::$app->params['dashboardEngine']) && \Yii::$app->params['dashboardEngine'] == WidgetAbstract::ENGINE_ROWS) {
            $this->setIconFramework(AmosIcons::IC);
            $this->setIcon('propostecollaborazione');
            $paramsClassSpan = [];
        } else {
            $this->setIcon('partnership-profiles');
        }

        $this->setUrl(['/partnershipprofiles/expressions-of-interest/facilitator-expressions-of-interest']);
        $this->setCode('PARTNER_PROF_EXPR_OF_INT_EXPR_OF_INT');
        $this->setModuleName('partnershipprofiles');
        $this->setNamespace(__CLASS__);

        $this->setClassSpan(
            ArrayHelper::merge(
                $this->getClassSpan(),
                $paramsClassSpan
            )
        );
    }

}
