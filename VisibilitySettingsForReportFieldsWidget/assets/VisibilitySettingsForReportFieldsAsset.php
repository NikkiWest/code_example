<?php

namespace app\widgets\VisibilitySettingsForReportFieldsWidget\assets;

use yii\web\AssetBundle;

class VisibilitySettingsForReportFieldsAsset extends AssetBundle
{
    public $sourcePath = '@app/widgets/VisibilitySettingsForReportFieldsWidget/resources';

    public $css = [

    ];

    public $js = [
        'js/visibility-settings-for-report-fields.js'
    ];

    public $depends = [
        'app\assets\AppAsset',
    ];
}
