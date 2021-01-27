<?php


namespace app\models\form;


use app\models\db\ReportFieldsSetting;
use yii\base\Model;

class ReportFieldsSettingForm extends Model
{
    public $fieldsVisible; // может прийти и строка и массив от формы
    public string $reportName;

    public function attributeLabels()
    {
        return [
            'reportName' => 'Название отчета',
            'fieldsVisible' => 'Поля для показа',
        ];
    }

    public function rules()
    {
        return [
            [['reportName'], 'string', 'max' => 255],
            [['fieldsVisible'], 'safe'],
        ];
    }

    public function beforeValidate()
    {
        if (empty($this->fieldsVisible)){
            $this->addError('fieldsVisible', 'Выберите хотя бы один столбец для показа');
            return false;
        }
        return parent::beforeValidate();
    }

    public function save()
    {
        if (!$this->validate()) return false;
        $ReportFieldsSetting = ReportFieldsSetting::findOne(['report_name' => $this->reportName]);
        if (empty($ReportFieldsSetting)){
            $ReportFieldsSetting = new ReportFieldsSetting();
            $ReportFieldsSetting->report_name = $this->reportName;
        }
        $ReportFieldsSetting->fields_visible = $this->fieldsVisible;
        return $ReportFieldsSetting->save();
    }
}