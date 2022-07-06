<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    @vendor/open20/amos-partnership-profiles/src/views
 */

use open20\amos\core\helpers\Html;
use open20\amos\core\views\DataProviderView;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var open20\amos\partnershipprofiles\models\search\PartnershipProfilesCategorySearch $model
 */

$this->title = \open20\amos\partnershipprofiles\Module::t('amospartnershipprofiles', 'Le categorie');
$this->params['breadcrumbs'][] = ['label' => '', 'url' => ['/partnershipprofiles']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partnership-profiles-category-index">
    <?= $this->render('_search', ['model' => $model, 'originAction' => Yii::$app->controller->action->id]); ?>

    <?= DataProviderView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'currentView' => $currentView,
        'gridView' => [
            'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],

            'columns' => [
                //'filterModel' => $model,
                [
                    'label' => $model->getAttributeLabel('categoryIcon'),
                    'format' => 'html',
                    'value' => function ($model) {
                        /** @var NewsCategorie $model */
                        $file = $model->categoryIcon;
                        if($file){
                            $url = $file->getWebUrl();
                            $contentImage = Html::img($url, ['class' => 'gridview-image', 'alt' => $model->getAttributeLabel('categoryIcon')]);
                            return $contentImage;
                        }

                    }
                ],
                'title',
//                'color_background',
                [
                    'class' => 'open20\amos\core\views\grid\ActionColumn',
                ],
            ],
        ],
        /*'listView' => [
        'itemView' => '_item',
        'masonry' => FALSE,

        // Se masonry settato a TRUE decommentare e settare i parametri seguenti 
        // nel CSS settare i seguenti parametri necessari al funzionamento tipo
        // .grid-sizer, .grid-item {width: 50&;}
        // Per i dettagli recarsi sul sito http://masonry.desandro.com                                     

        //'masonrySelector' => '.grid',
        //'masonryOptions' => [
        //    'itemSelector' => '.grid-item',
        //    'columnWidth' => '.grid-sizer',
        //    'percentPosition' => 'true',
        //    'gutter' => '20'
        //]
        ],
        'iconView' => [
        'itemView' => '_icon'
        ],
        'mapView' => [
        'itemView' => '_map',          
        'markerConfig' => [
        'lat' => 'domicilio_lat',
        'lng' => 'domicilio_lon',
        'icon' => 'iconMarker',
        ]
        ],
        'calendarView' => [
        'itemView' => '_calendar',
        'clientOptions' => [
        //'lang'=> 'de'
        ],
        'eventConfig' => [
        //'title' => 'titleEvent',
        //'start' => 'data_inizio',
        //'end' => 'data_fine',
        //'color' => 'colorEvent',
        //'url' => 'urlEvent'
        ],
        'array' => false,//se ci sono più eventi legati al singolo record
        //'getEventi' => 'getEvents'//funzione da abilitare e implementare nel model per creare un array di eventi legati al record
        ]*/
    ]); ?>

</div>
