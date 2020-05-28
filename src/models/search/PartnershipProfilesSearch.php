<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\partnershipprofiles\models\search
 * @category   CategoryName
 */

namespace open20\amos\partnershipprofiles\models\search;

use open20\amos\cwh\query\CwhActiveQuery;
use open20\amos\notificationmanager\base\NotifyWidget;
use open20\amos\notificationmanager\base\NotifyWidgetDoNothing;
use open20\amos\notificationmanager\models\NotificationChannels;
use open20\amos\partnershipprofiles\models\PartnershipProfiles;
use open20\amos\tag\models\EntitysTagsMm;
use open20\amos\core\interfaces\SearchModelInterface;
use open20\amos\core\record\SearchResult;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\di\Container;
use yii\helpers\ArrayHelper;
use Yii;
use yii\data\Pagination;

/**
 * Class PartnershipProfilesSearch
 * PartnershipProfilesSearch represents the model behind the search form about `open20\amos\partnershipprofiles\models\PartnershipProfiles`.
 * @package open20\amos\partnershipprofiles\models\search
 */
class PartnershipProfilesSearch extends PartnershipProfiles implements SearchModelInterface
{
    /**
     * @var Container $container
     */
    private $container;

    /**
     * @inheritdoc
     */
    public function __construct(array $config = [])
    {
        $this->container = new Container();
        $this->container->set('notify', new NotifyWidgetDoNothing());
        parent::__construct($config);
    }

    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function getNotifier()
    {
        return $this->container->get('notify');
    }

    /**
     * @param $notifier
     */
    public function setNotifier(NotifyWidget $notifier)
    {
        $this->container->set('notify', $notifier);
    }

    /**
     * @param ActiveQuery $query
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    private function notificationOff($query)
    {
        $notify = $this->getNotifier();
        if ($notify) {
            /** @var \open20\amos\notificationmanager\AmosNotify $notify */
            $notify->notificationOff(\Yii::$app->getUser()->id, PartnershipProfiles::className(), $query, NotificationChannels::CHANNEL_READ);
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
            [[
                'status',
                'title',
                'short_description',
                'extended_description',
                'expected_contribution',
                'advantages_innovative_aspects',
                'contact_person',
                'other_prospect_desired_collab',
                'partnership_profile_date',
                'english_title',
                'english_short_description',
                'english_extended_description',
                'other_work_language',
                'other_development_stage',
                'other_intellectual_property',
                'partnership_profile_date_from',
                'partnership_profile_date_to',
                'attrPartnershipProfilesTypesMm',
                'work_language_id',
                'development_stage_id',
                'intellectual_property_id',
                'created_at',
                'updated_at',
                'deleted_at'
            ], 'safe'],
            [[
                'expiration_in_months',
                'willingness_foreign_partners'
            ], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $parentBehaviors = parent::behaviors();

        $behaviors = [];
        // If the parent model PartnershipProfiles is a model enabled for tags, PartnershipProfilesSearch will have TaggableBehavior too
        $moduleTag = \Yii::$app->getModule('tag');
        if (isset($moduleTag) && in_array(PartnershipProfiles::className(), $moduleTag->modelsEnabled) && $moduleTag->behaviors) {
            $behaviors = ArrayHelper::merge($moduleTag->behaviors, $behaviors);
        }

        return ArrayHelper::merge($parentBehaviors, $behaviors);
    }

    /**
     * This is the base search.
     * @param array $params
     * @return ActiveQuery
     */
    public function baseSearch($params)
    {
        /** @var ActiveQuery $query */
        $query = PartnershipProfiles::find()->distinct();

        $query->joinWith('partnershipProfilesTypesMms');
        $query->joinWith('workLanguage');
        $query->joinWith('developmentStage');
        $query->joinWith('intellectualProperty');

        // Init the default search values
        $this->initOrderVars();

        // Check params to get orders value
        $this->setOrderVars($params);

        // Add the search by tags query part
        $this->addSearchByTagsQueryPart($query, $params);

        return $query;
    }

    /**
     * @param ActiveQuery $query
     * @param array $params
     * @return ActiveQuery
     */
    protected function addSearchByTagsQueryPart($query, $params)
    {
        $moduleTag = \Yii::$app->getModule('tag');
        if (isset($moduleTag) && in_array(PartnershipProfiles::className(), $moduleTag->modelsEnabled) && $moduleTag->behaviors) {
            if (isset($params[$this->formName()]['tagValues'])) {
                $tagValues = $params[$this->formName()]['tagValues'];
                $this->setTagValues($tagValues);
                if (is_array($tagValues) && !empty($tagValues)) {
                    $andWhere = "";
                    $i = 0;
                    foreach ($tagValues as $rootId => $tagId) {
                        if (!empty($tagId)) {
                            if ($i == 0) {
                                $query->innerJoin(EntitysTagsMm::tableName() . ' entities_tag',
                                    "entities_tag.classname = '" . addslashes(PartnershipProfiles::className()) . "' AND entities_tag.record_id = " . PartnershipProfiles::tableName() . ".id");
                            } else {
                                $andWhere .= " OR ";
                            }
                            $andWhere .= "(entities_tag.tag_id in (" . $tagId . ") AND entities_tag.root_id = " . $rootId . " AND entities_tag.deleted_at IS NULL)";
                            $i++;
                        }
                    }
                    if (!empty($andWhere)) {
                        $query->andWhere($andWhere);
                    }
                }
            }
        }
        return $query;
    }

    /**
     * @param ActiveQuery $query
     * @return ActiveQuery
     */
    public function baseFilter($query)
    {
        $query->andFilterWhere(['like', self::tableName() . '.title', $this->title])
            ->andFilterWhere(['like', self::tableName() . '.short_description', $this->short_description]);

        return $query;
    }

    /**
     * @param ActiveDataProvider $dataProvider
     */
    protected function setSearchSort($dataProvider)
    {
        // Check if can use the custom module order
        /*if ($this->canUseModuleOrder()) {
            $order = $this->createOrderClause();
            $dataProvider->setSort($order);
        }*/
        if ($this->canUseModuleOrder()) {
            $dataProvider->setSort([
                'defaultOrder' => [
                    $this->orderAttribute => (int)$this->orderType
                ]
            ]);
        } else { //for widget graphic last news, order is incorrect without this else
            $dataProvider->setSort([
                'defaultOrder' => [
                    'created_at' => SORT_DESC
                ]
            ]);
        }
    }

    /**
     * @param array $params
     * @return ActiveQuery
     */
    public function searchQuery($params)
    {
        $query = $this->baseSearch($params);
        $classname = PartnershipProfiles::className();
        $moduleCwh = \Yii::$app->getModule('cwh');
        $cwhActiveQuery = null;

        if (isset($moduleCwh)) {
            /** @var \open20\amos\cwh\AmosCwh $moduleCwh */
            $moduleCwh->setCwhScopeFromSession();
            $cwhActiveQuery = new CwhActiveQuery(
                $classname, [
                'queryBase' => $query
            ]);
        }
        $isSetCwh = $this->isSetCwh($moduleCwh, $classname);
        if ($isSetCwh) {
            $query = $cwhActiveQuery->getQueryCwhOwnInterest();
        } else {
            $query->andWhere(['status' => [self::PARTNERSHIP_PROFILES_WORKFLOW_STATUS_VALIDATED, self::PARTNERSHIP_PROFILES_WORKFLOW_STATUS_FEEDBACKRECEIVED]]);
        }
        return $query;
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function search($params)
    {
        $query = $this->searchQuery($params);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->setSearchSort($dataProvider);
        $this->notificationOff($query);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->baseFilter($query);

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return ActiveQuery
     */
    public function searchAllQuery($params)
    {
        $query = $this->baseSearch($params);
        $classname = PartnershipProfiles::className();
        $moduleCwh = \Yii::$app->getModule('cwh');
        $cwhActiveQuery = null;
        if (isset($moduleCwh)) {
            /** @var \open20\amos\cwh\AmosCwh $moduleCwh */
            $moduleCwh->setCwhScopeFromSession();
            $cwhActiveQuery = new CwhActiveQuery(
                $classname, [
                'queryBase' => $query
            ]);
        }
        $isSetCwh = $this->isSetCwh($moduleCwh, $classname);
        if ($isSetCwh) {
            $query = $cwhActiveQuery->getQueryCwhAll();
        } else {
            $query->andWhere(['status' => [self::PARTNERSHIP_PROFILES_WORKFLOW_STATUS_VALIDATED, self::PARTNERSHIP_PROFILES_WORKFLOW_STATUS_FEEDBACKRECEIVED]]);
        }
        return $query;
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function searchAll($params)
    {
        $query = $this->searchAllQuery($params);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->setSearchSort($dataProvider);
        $this->notificationOff($query);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->baseFilter($query);

        return $dataProvider;
    }

    /**
     * @return ActiveQuery
     */
    public function searchAllAdminQuery()
    {
        /** @var ActiveQuery $query */
        $query = PartnershipProfiles::find()->distinct();
        $query->joinWith('partnershipProfilesTypesMms');
        $query->joinWith('workLanguage');
        $query->joinWith('developmentStage');
        $query->joinWith('intellectualProperty');
        return $query;
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function searchAllAdmin($params)
    {
        $query = $this->searchAllAdminQuery();

        // Init the default search values
        $this->initOrderVars();

        // Check params to get orders value
        $this->setOrderVars($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->setSearchSort($dataProvider);
        $this->notificationOff($query);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->baseFilter($query);

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return ActiveQuery
     */
    public function searchCreatedByQuery($params)
    {
        $query = $this->baseSearch($params);
        $classname = PartnershipProfiles::className();
        $moduleCwh = \Yii::$app->getModule('cwh');
        $cwhActiveQuery = null;
        if (isset($moduleCwh)) {
            /** @var \open20\amos\cwh\AmosCwh $moduleCwh */
            $moduleCwh->setCwhScopeFromSession();
            $cwhActiveQuery = new CwhActiveQuery(
                $classname, [
                'queryBase' => $query
            ]);
        }
        $isSetCwh = $this->isSetCwh($moduleCwh, $classname);
        if ($isSetCwh) {
            $query = $cwhActiveQuery->getQueryCwhOwn();
        } else {
            $query->andWhere([self::tableName() . '.created_by' => \Yii::$app->user->getId()]);
        }
        return $query;
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function searchCreatedBy($params)
    {
        $query = $this->searchCreatedByQuery($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->setSearchSort($dataProvider);
        $this->notificationOff($query);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->baseFilter($query);

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return ActiveQuery
     */
    public function searchToValidateQuery($params)
    {
        $query = $this->baseSearch($params);
        $classname = PartnershipProfiles::className();
        $moduleCwh = \Yii::$app->getModule('cwh');
        $cwhActiveQuery = null;

        if (isset($moduleCwh)) {
            /** @var \open20\amos\cwh\AmosCwh $moduleCwh */
            $moduleCwh->setCwhScopeFromSession();
            $cwhActiveQuery = new CwhActiveQuery(
                $classname, [
                'queryBase' => $query
            ]);
        }
        $isSetCwh = $this->isSetCwh($moduleCwh, $classname);
        if ($isSetCwh) {
            $query = $cwhActiveQuery->getQueryCwhToValidate();
        } else {
            $query->andWhere(['status' => self::PARTNERSHIP_PROFILES_WORKFLOW_STATUS_TOVALIDATE]);
        }
        return $query;
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function searchToValidate($params)
    {
        $query = $this->searchToValidateQuery($params);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->setSearchSort($dataProvider);
        $this->notificationOff($query);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->baseFilter($query);

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return ActiveQuery
     */
    public function searchArchivedQuery($params)
    {
        $query = $this->baseSearch($params);
        $query->andWhere(['status' => self::PARTNERSHIP_PROFILES_WORKFLOW_STATUS_ARCHIVED]);
        return $query;
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function searchArchived($params)
    {
        $query = $this->searchArchivedQuery($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->setSearchSort($dataProvider);
        $this->notificationOff($query);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->baseFilter($query);

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return ActiveQuery
     */
    public function searchClosedQuery($params)
    {
        $query = $this->baseSearch($params);
        $query->andWhere(['status' => self::PARTNERSHIP_PROFILES_WORKFLOW_STATUS_CLOSED]);
        return $query;
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function searchClosed($params)
    {
        $query = $this->searchClosedQuery($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->setSearchSort($dataProvider);
        $this->notificationOff($query);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->baseFilter($query);

        return $dataProvider;
    }

    /**
     * @param array $params
     * @return ActiveQuery
     */
    public function searchForFacilitatorQuery($params)
    {
        $query = $this->baseSearch($params);
        $query->andWhere(['partnership_profile_facilitator_id' => \Yii::$app->user->id]);
        $query->andWhere(['<>', 'status', PartnershipProfiles::PARTNERSHIP_PROFILES_WORKFLOW_STATUS_DRAFT]);
        return $query;
    }

    /**
     * Search for facilitator.
     * @param array $params
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function searchForFacilitator($params)
    {
        $query = $this->searchForFacilitatorQuery($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->setSearchSort($dataProvider);
        $this->notificationOff($query);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $this->baseFilter($query);

        return $dataProvider;
    }

    /**
     * @param $moduleCwh
     * @param $classname
     * @return bool
     */
    private function isSetCwh($moduleCwh, $classname)
    {
        if (isset($moduleCwh) && in_array($classname, $moduleCwh->modelsEnabled)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param array $params
     * @param null $limit
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\di\NotInstantiableException
     */
    public function latestPartenershipProfilesSearch($params, $limit = null)
    {
        $dataProvider = $this->searchAll($params);
        $dataProvider->query->orderBy(['created_at' => SORT_DESC]);
        $dataProvider->pagination->pageSize = $limit;
        return $dataProvider;
    }
    
     /**
     * Search all validated documents
     *
     * @param array $searchParamsArray Array of search words
     * @param int|null $pageSize
     * @return ActiveDataProvider
     */
    public function globalSearch($searchParamsArray, $pageSize = 5) {
        $dataProvider = $this->searchAll([]);
        $pagination = $dataProvider->getPagination();
        if (!$pagination) {
            $pagination = new Pagination();
            $dataProvider->setPagination($pagination);
        }
        $pagination->setPageSize($pageSize);
        
        // Verifico se il modulo supporta i TAG e, in caso, ricerco anche fra quelli
        $moduleTag = \Yii::$app->getModule('tag');
        $enableTagSearch = isset($moduleTag) && in_array(PartnershipProfiles::className(), $moduleTag->modelsEnabled);

        if ($enableTagSearch) {
            $dataProvider->query->leftJoin('entitys_tags_mm e_tag', "e_tag.record_id=" . PartnershipProfiles::tableName() . ".id AND e_tag.deleted_at IS NULL AND e_tag.classname='" . addslashes(PartnershipProfiles::className()) . "'");

//            if (Yii::$app->db->schema->getTableSchema('tag__translation')) {
//                // Esiste la tabella delle traduzioni dei TAG. Uso quella per la ricerca
//                $dataProvider->query->leftJoin('tag__translation tt', "e_tag.tag_id=tt.tag_id");
//                $tagTranslationSearch = true;
//            }
            
            $dataProvider->query->leftJoin('tag t', "e_tag.tag_id=t.id");
        }
        
                
        foreach ($searchParamsArray as $searchString) {
            $orQueries = [
                'or',
                ['like', self::tableName() .'.title', $searchString],
                ['like', self::tableName() .'.short_description', $searchString],
                ['like', self::tableName() .'.extended_description', $searchString],
                ['like', self::tableName() .'.advantages_innovative_aspects', $searchString],
                ['like', self::tableName() .'.other_prospect_desired_collab', $searchString],
                ['like', self::tableName() .'.expected_contribution', $searchString],
                ['like', self::tableName() .'.contact_person', $searchString],
                ['like', self::tableName() .'.english_title', $searchString],
                ['like', self::tableName() .'.english_short_description', $searchString],
                ['like', self::tableName() .'.english_extended_description', $searchString],
            ];

            $tagsValues = \Yii::$app->request->get('tagValues');
            if ($enableTagSearch) {
                $arrayTagIds = [];
                if(!empty($tagsValues)) {
                    $tagIds = ArrayHelper::merge($arrayTagIds, explode(',', $tagsValues));
                    $dataProvider->query->andFilterWhere(['t.id' => $tagIds]);
                }

//                if ($tagTranslationSearch) {
//                    $orQueries[] = ['like', 'tt.nome', $searchString];
//                }
//                $orQueries[] = ['like', 't.nome', $searchString];
            }
            else {
                if (!empty($tagsValues)) {
                    $dataProvider->query->andWhere(0);
                }
            }

            $dataProvider->query->andWhere($orQueries);
        }

        $searchModels = [];
        foreach ($dataProvider->models as $m) {
            array_push($searchModels, $this->convertToSearchResult($m));
        }
        $dataProvider->setModels($searchModels);

        return $dataProvider;
    }

    /**
     * @param object $model The model to convert into SearchResult
     * @return SearchResult 
     */
    public function convertToSearchResult($model) {
        $searchResult = new SearchResult();
        $searchResult->url = $model->getFullViewUrl();
        $searchResult->box_type = "none";
        $searchResult->id = $model->id;
        $searchResult->titolo = $model->title;
        $searchResult->data_pubblicazione = $model->partnership_profile_date;
        $searchResult->abstract = $model->short_description;
        return $searchResult;
    }
}
