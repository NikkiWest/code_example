<?php

namespace app\lib\helpers;

use app\models\db\ReportFieldsSetting;
use Yii;

class ReportFieldsSettingHelper
{
    public static function getSettingsForReport($reportName, $attributes): array
    {
        $user_id = Yii::$app->user->identity->id;
        $ReportFieldsSetting = ReportFieldsSetting::findOne(['user_id' => $user_id, 'report_name' => $reportName]);
        if (!empty($ReportFieldsSetting)) {
            return $ReportFieldsSetting->fields_visible;
        } else {
            return array_keys($attributes);
        }
    }
}
