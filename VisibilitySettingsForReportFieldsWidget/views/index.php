<?php
/**
 * @var View $this
 * @var array $fieldsList
 * @var ReportFieldsSettingForm $model
 */

use app\models\form\ReportFieldsSettingForm;
use app\widgets\VisibilitySettingsForReportFieldsWidget\assets\VisibilitySettingsForReportFieldsAsset;
use yii\base\InvalidConfigException;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\web\View;

try {
    $this->registerAssetBundle(VisibilitySettingsForReportFieldsAsset::class);
} catch (InvalidConfigException $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}

Modal::begin([
    'id' => 'modalSettingsColumns',
    'header' => 'Настройка столбцов',
    'toggleButton' => [
        'label' => 'Настройка столбцов',
        'tag' => 'div',
        'class' => 'btn btn-default btn-additional-option',
    ],
    'size' => 'modal-md'
]);
$form = ActiveForm::begin(['method' => 'post', 'options' => ['data-pjax' => 1], 'id' => 'form-settings-fields']);
echo $form->field($model, 'reportName')->hiddenInput()->label(false);
echo $form->field($model, 'fieldsVisible')->checkboxList($fieldsList);
echo Html::submitButton('Сохранить', ['class' => 'btn btn-default']);
ActiveForm::end();
Modal::end();
