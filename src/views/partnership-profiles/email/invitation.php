<?php
/**
 * Aria S.p.A.
 * OPEN 2.0
 *
 *
 * @package    open20\amos\community
 * @category   CategoryName
 */

use open20\amos\community\AmosCommunity;
use open20\amos\core\helpers\Html;

/** @var \open20\amos\community\utilities\EmailUtil $util */

?>

<div>
    <div style="box-sizing:border-box;">
        <div style="padding:5px 10px;background-color: #F2F2F2;">
            <h1 style="color:#297A38;text-align:center;font-size:1.5em;margin:0;"><?= AmosCommunity::t('amoscommunity', '#invitation_received_mail_title') ?></h1>
        </div>
        <div
            style="border:1px solid #cccccc;padding:10px;margin-bottom: 10px;background-color: #ffffff; margin-top: 20px;">
            <h2 style="font-size:2em;line-height: 1;"><?= $util->managerName . " " . AmosCommunity::t('amoscommunity', '#invitation_mail_text_1') . $util->contextLabel ?></h2>

            <div style="display: flex; padding: 10px;">
                <?php if ($util->isCommunityContext): ?>
                    <div
                        style="width: 50px; height: 50px; -webkit-border-radius: 50%; -moz-border-radius: 50%; border-radius: 50%;float: left;">
                        <?= \open20\amos\community\widgets\CommunityCardWidget::widget([
                            'model' => $util->community,
                            'onlyLogo' => true,
                            'absoluteUrl' => true
                        ]) ?>
                    </div>
                <?php endif; ?>
                <?php
                $divOptions = $util->isCommunityContext ? ['style' => 'margin: 0 0 0 20px;'] : [];
                echo Html::tag('div', '<p style="font-weight: 900">' . $util->community->name . '</p>
                <p>' . $util->community->getDescription(true) . '</p>', $divOptions)
                ?>
            </div>
            <div style="width:100%;margin-top:30px">
                <p>
                    <?= Html::a(AmosCommunity::t('amoscommunity', 'Sign into the platflorm'), $util->url, ['style' => 'color: green;']) . ' ' ?>
                    <?= AmosCommunity::t('amoscommunity', "to accept or reject the invitation.") ?>
                </p>
            </div>
            <?php if ($util->isCommunityContext): ?>
                <p>
                    <?= AmosCommunity::t('amoscommunity', "#mail_network_community_1") ?>
                    <?= Html::a(' ' . AmosCommunity::t('amoscommunity', '#mail_network_community_2'),
                        Yii::$app->urlManager->createAbsoluteUrl('dashboard'),
                        ['style' => 'color: green;']
                    ) ?>
                    <?= ' ' . AmosCommunity::t('amoscommunity', '#mail_network_community_3') . ' ' ?>
                    <span
                        style="font-weight: 900; font-style: italic;"><?= AmosCommunity::t('amoscommunity', '#mail_network_community_4') ?> </span>
                    <?= AmosCommunity::t('amoscommunity', '#mail_network_community_5') ?>
                </p>
            <?php endif; ?>
        </div>
    </div>
</div>