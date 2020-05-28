<?php

/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\partnershipprofiles\widgets
 * @category   CategoryName
 */

namespace open20\amos\partnershipprofiles\widgets;

use open20\amos\core\helpers\Html;
use open20\amos\partnershipprofiles\exceptions\PartnershipProfilesException;
use open20\amos\partnershipprofiles\models\PartnershipProfiles;
use open20\amos\partnershipprofiles\Module;
use yii\base\Widget;

/**
 * Class ExpressYourInterestWidget
 * @package open20\amos\partnershipprofiles\widgets
 */
class ExpressYourInterestWidget extends Widget
{
    /**
     * @var string $layout
     */
    public $layout = '{expressYourInterestButton}';

    /**
     * @var PartnershipProfiles $model
     */
    private $model;

    /**
     * @var int[]|null $allowedPartnershipProfileIds
     */
    private $allowedPartnershipProfileIds = null;

    /**
     * @throws PartnershipProfilesException
     */
    public function init()
    {
        parent::init();
        
        if (is_null($this->model)) {
            throw new PartnershipProfilesException(Module::t('amospartnershipprofiles', 'ExpressYourInterestWidget: missing model'));
        }
    }

    /**
     * @return PartnershipProfiles
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param PartnershipProfiles $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return int[]
     */
    public function getAllowedPartnershipProfileIds()
    {
        return $this->allowedPartnershipProfileIds;
    }

    /**
     * @param int[] $allowedPartnershipProfileIds
     */
    public function setAllowedPartnershipProfileIds($allowedPartnershipProfileIds)
    {
        $this->allowedPartnershipProfileIds = $allowedPartnershipProfileIds;
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $content = preg_replace_callback("/{\\w+}/", function ($matches) {
            $content = $this->renderSection($matches[0]);
            return $content === false ? $matches[0] : $content;
        }, $this->layout);
        return $content;
    }

    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     * @param string $name the section name, e.g., `{summary}`, `{items}`.
     * @return string|boolean the rendering result of the section, or false if the named section is not supported.
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{expressYourInterestButton}':
                return $this->renderExpressYourInterestButton();
            default:
                return false;
        }
    }

    /**
     * Render the "Express your interest" button.
     * @return string
     */
    public function renderExpressYourInterestButton()
    {
        $button = '';
        if ($this->model->expressionOfInterestAllowed($this->allowedPartnershipProfileIds)) {
            $button = Html::beginTag('div', ['class' => 'footer_sidebar text-right']);
            $button .= Html::a(
                Module::tHtml('amospartnershipprofiles', 'Express your interest'),
                ['/partnershipprofiles/expressions-of-interest/create', 'partnership_profile_id' => $this->model->id],
                ['class' => 'btn btn-navigation-primary']
            );
            $button .= Html::endTag('div');
        }
        return $button;
    }
}
