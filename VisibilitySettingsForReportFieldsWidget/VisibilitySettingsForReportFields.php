<?php

namespace app\widgets\VisibilitySettingsForReportFieldsWidget;

use app\lib\helpers\ReportFieldsSettingHelper;
use app\models\form\ReportFieldsSettingForm;
use yii\bootstrap\Widget;

class VisibilitySettingsForReportFields extends Widget
{
    public string $reportName = '';
    public ?array $fields = [];

    /**
     * PopupableCell constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        if (isset($config['reportName']) && isset($config['fields'])) {
            $this->reportName = $config['reportName'];
            $this->fields = $config['fields'];
        }
        parent::__construct($config);
    }

    /**
     * @return string
     */
    public function run(): string
    {
        $SettingsFieldsReportForm = new ReportFieldsSettingForm();
        $SettingsFieldsReportForm->fieldsVisible = ReportFieldsSettingHelper::getSettingsForReport($this->reportName, $this->fields);
        $SettingsFieldsReportForm->reportName = $this->reportName;
        return $this->render('index', [
            'fieldsList' => $this->fields,
            'model' => $SettingsFieldsReportForm
        ]);
    }

}
