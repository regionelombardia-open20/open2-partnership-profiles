<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\partnershipprofiles\controllers
 * @category   CategoryName
 */

namespace open20\amos\partnershipprofiles\controllers;

use open20\amos\partnershipprofiles\models\ExpressionsOfInterest;
use open20\amos\partnershipprofiles\models\PartnershipProfiles;
use open20\amos\partnershipprofiles\Module;
use open20\amos\partnershipprofiles\rules\ReadAllExprOfIntRule;
use open20\amos\partnershipprofiles\utility\ExpressionsOfInterestUtility;
use open20\amos\partnershipprofiles\widgets\icons\WidgetIconPartnerProfExprOfIntDashboard;
use raoul2000\workflow\base\WorkflowException;
use Yii;
use yii\base\InvalidParamException;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;

/**
 * Class ExpressionsOfInterestController
 * This is the class for controller "ExpressionsOfInterestController".
 * @package open20\amos\partnershipprofiles\controllers
 */
class ExpressionsOfInterestController extends \open20\amos\partnershipprofiles\controllers\base\ExpressionsOfInterestController
{
    /**
     * @var string $actionByUrl
     */
    protected $actionByUrl = '';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'all',
                            'all-admin',
                            'received',
                            'created-by',
                            'facilitator-expressions-of-interest',
                            'validate',
                            'reject'
                        ],
                        'roles' => ['EXPRESSIONS_OF_INTEREST_ADMINISTRATOR']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'all',
                            'received',
                        ],
                        'roles' => ['PARTNERSHIP_PROFILES_READER']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'all',
                            'created-by',
                        ],
                        'roles' => ['EXPRESSIONS_OF_INTEREST_CREATOR']
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'facilitator-expressions-of-interest',
                            'validate',
                            'reject'
                        ],
                        'roles' => ['PARTNER_PROF_EXPR_OF_INT_ADMIN_FACILITATOR']
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get']
                ]
            ]
        ]);
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    protected function setCustomModule()
    {
        parent::setCustomModule();

        if ($this->actionByUrl == 'facilitator-expressions-of-interest') {
            $this->customModule = Module::PARTNERPROFEXPROFINT;
        }
    }

    /**
     * @param string|null $layout
     * @param int|null $partnership_profile_id
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionIndex($layout = null)
    {
        throw new ForbiddenHttpException('');
    }

    /**
     * Base operations for list views
     * @param string $pageTitle
     * @param ActiveDataProvider $dataProvider
     * @param bool $setCurrentDashboard
     * @param int|null $partnership_profile_id
     * @return string
     */
    private function baseListsAction($pageTitle, $setCurrentDashboard = true, $dataProvider, $partnership_profile_id = null, $child_of = null)
    {
        Url::remember();
        if ($partnership_profile_id) {
            $dataProvider->query->andWhere(['partnership_profile_id' => $partnership_profile_id]);
        }
        $this->setDataProvider($dataProvider);
        $this->setTitleAndBreadcrumbs($pageTitle);
        $this->setListViewsParams($setCurrentDashboard, $child_of);
        return $this->render('index', [
            'dataProvider' => $this->getDataProvider(),
            'model' => $this->modelSearch,
            'currentView' => $this->getCurrentView(),
            'availableViews' => $this->getAvailableViews(),
            'url' => ($this->url) ? $this->url : null,
            'parametro' => ($this->parametro) ? $this->parametro : null
        ]);
    }

    /**
     * @param int|null $partnership_profile_id
     * @return string
     */
    public function actionAllAdmin($partnership_profile_id = null)
    {
        Url::remember();
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->modelSearch->searchAllAdmin(Yii::$app->request->getQueryParams());
        return $this->baseListsAction('All expressions of interest', false, $dataProvider, $partnership_profile_id);
    }

    /**
     * @param int|null $partnership_profile_id
     * @return string
     * @throws ForbiddenHttpException
     */
    public function actionAll($partnership_profile_id = null)
    {
        Yii::$app->view->params['textHelp'] = ['filename' => 'all_expressions_of_interest_help'];
        Url::remember();

        // START Check permission
        $partnershipProfile = PartnershipProfiles::findOne($partnership_profile_id);
        if (empty($partnershipProfile)){
            throw new InvalidParamException();
        }
        if (!(\Yii::$app->user->can(ReadAllExprOfIntRule::className(), ['model' => $partnershipProfile]))) {
            throw new ForbiddenHttpException();
        }
        // END Check permission

        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->modelSearch->searchAll(Yii::$app->request->getQueryParams());
        if (($partnershipProfile->created_by == Yii::$app->user->id) || ($partnershipProfile->partnership_profile_facilitator_id == Yii::$app->user->id)) {
            $dataProvider = $this->modelSearch->searchAll(Yii::$app->request->getQueryParams());
        } elseif (ExpressionsOfInterestUtility::isPartProfExprsOfIntCreator($partnershipProfile)) {
            $dataProvider = $this->modelSearch->searchAllAuthor(Yii::$app->request->getQueryParams());
        } elseif (ExpressionsOfInterestUtility::isPartProfExprsOfIntCreatorFacilitator($partnershipProfile)) {
            $dataProvider = $this->modelSearch->searchAllAuthorFacilitator(Yii::$app->request->getQueryParams());
        }
        return $this->baseListsAction('All', false, $dataProvider, $partnership_profile_id);
    }

    /**
     * @param int|null $partnership_profile_id
     * @return string
     */
    public function actionReceived($partnership_profile_id = null)
    {
        Url::remember();
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->modelSearch->searchReceived(Yii::$app->request->getQueryParams());
        Yii::$app->view->params['textHelp']['filename'] = 'yours_interest_created_challenges-helper';
        return $this->baseListsAction('Received', true, $dataProvider, $partnership_profile_id);
    }

    /**
     * @param int|null $partnership_profile_id
     * @return string
     */
    public function actionCreatedBy($partnership_profile_id = null)
    {
        Url::remember();
        /** @var ActiveDataProvider $dataProvider */
        $dataProvider = $this->modelSearch->searchCreatedBy(Yii::$app->request->getQueryParams());
        Yii::$app->view->params['textHelp']['filename'] = 'yours_interest_created_challenges-helper';

        $this->setAvailableViews([
            'grid' => $this->viewGrid
        ]);
        $this->setCurrentView($this->getAvailableView('grid'));
        return $this->baseListsAction('Created By', true, $dataProvider, $partnership_profile_id);
    }

    /**
     * @param string|null $currentView
     * @return string
     */
    public function actionFacilitatorExpressionsOfInterest()
    {
        $dataProvider = $this->modelSearch->searchForFacilitator(Yii::$app->request->getQueryParams());
        return $this->baseListsAction('Expressions of interest', true, $dataProvider, null, WidgetIconPartnerProfExprOfIntDashboard::className());
    }

    /**
     * @param int $id Expression of Interest id.
     * @return \yii\web\Response
     */
    public function actionValidate($id)
    {
        $expressionofint = ExpressionsOfInterest::findOne($id);
        try {
            $expressionofint->sendToStatus(ExpressionsOfInterest::EXPRESSIONS_OF_INTEREST_WORKFLOW_STATUS_ACTIVE);
            $ok = $expressionofint->save(false);
            if ($ok) {
                Yii::$app->session->addFlash('success', Module::t('amospartnershipprofiles', '#expressionofinterestvalidated'));
            } else {
                Yii::$app->session->addFlash('danger', Module::t('amospartnershipprofiles', '#ERROR_WHILE_VALIDATING_EXPRESSION'));
            }
        } catch (WorkflowException $e) {
            Yii::$app->session->addFlash('danger', $e->getMessage());
            return $this->redirect(Url::previous());
        }
        return $this->redirect(Url::previous());
    }

    /**
     * @param int $id Expression of Interest id.
     * @return \yii\web\Response
     */
    public function actionReject($id)
    {
        $expressionofint = ExpressionsOfInterest::findOne($id);
        try {
            $expressionofint->sendToStatus(ExpressionsOfInterest::EXPRESSIONS_OF_INTEREST_WORKFLOW_STATUS_REJECTED);
            $ok = $expressionofint->save(false);
            if ($ok) {
                Yii::$app->session->addFlash('success', Module::t('amospartnershipprofiles', '#expressionofinterestrejected'));
            } else {
                Yii::$app->session->addFlash('danger', Module::t('amospartnershipprofiles', '#ERROR_WHILE_VALIDATING_EXPRESSION'));
            }
        } catch (WorkflowException $e) {
            Yii::$app->session->addFlash('danger', $e->getMessage());
            return $this->redirect(Url::previous());
        }
        return $this->redirect(Url::previous());
    }
}
