<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\partnershipprofiles\views\partnership-profiles
 * @category   CategoryName
 */

use open20\amos\core\views\DataProviderView;
use open20\amos\partnershipprofiles\controllers\PartnershipProfilesController;
use open20\amos\partnershipprofiles\Module;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\partnershipprofiles\models\search\PartnershipProfilesSearch $model
 * @var string $currentView
 */

$this->title = Module::t('amospartnershipprofiles', 'Partnership Profiles');
$this->params['breadcrumbs'][] = $this->title;
if (Yii::$app->user->can('AUDIT_PROPOSTE') || Yii::$app->user->isGuest) {
    $this->params['forceBreadcrumbs'][] = ['label' => 'Proposte dalla piattaforma 2022'];
}

/** @var PartnershipProfilesController $appController */
$appController = Yii::$app->controller;
if (!Yii::$app->user->isGuest) {
    $ownInterestPartnershipProfileIds = $appController->getOwnInterestPartnershipProfiles(true);
}

?>
<div class="<?= Yii::$app->controller->id ?>-index">
    <?= $this->render('_search', ['model' => $model]); ?>
    <?= $this->render('_order', ['model' => $model]); ?>

    <?= DataProviderView::widget([
        'dataProvider' => $dataProvider,
        'currentView' => $currentView,
        'gridView' => [
            'columns' => $model->getGridViewColumns()
        ],
        'listView' => [
            'itemView' => '_item',
            'viewParams' => [
                'ownInterestPartnershipProfileIds' => $ownInterestPartnershipProfileIds
            ],
            'showItemToolbar' => false,
        ]
    ]); ?>
</div>
