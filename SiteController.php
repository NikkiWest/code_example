<?php

namespace app\controllers;

use app\models\form\ReportFieldsSettingForm;
use app\models\Result;
use Yii;
use yii\bootstrap\Html;
use yii\filters\ContentNegotiator;
use yii\web\Response;

/**
 * Class SiteController
 * @package app\controllers
 */
class SiteController extends BaseWebController
{

    /**
     * @inheritdoc
     */
    public function behaviors(): array
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'only' => [
                    'save-setting-fields'
                ],
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
        ]);
    }


    /**
     * @inheritdoc
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionSaveSettingFields(): array
    {
        $ReportFieldsSettingForm = new ReportFieldsSettingForm();
        if ($ReportFieldsSettingForm->load(Yii::$app->request->post()) && $ReportFieldsSettingForm->save()) {
            return Result::json(true, 'Выбор полей сохранен');
        } else {
            return Result::json(false, Html::errorSummary($ReportFieldsSettingForm));
        }
    }
}
