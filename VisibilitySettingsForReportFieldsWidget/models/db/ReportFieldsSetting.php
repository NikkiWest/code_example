<?php

namespace app\models\db;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "public.report_fields_setting"
 *
 * @property int $id
 * @property int $user_id
 * @property string $report_name
 * @property array $fields_visible
 */
class ReportFieldsSetting extends ActiveRecord
{
    public function __construct(array $config = [])
    {
        $this->user_id = Yii::$app->user->identity->id ?? null;
        parent::__construct($config);
    }

    public static function tableName(): string
    {
        return 'public.report_fields_setting';
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь ID',
            'report_name' => 'Название отчета',
            'fields_visible' => 'Видимые поля',
        ];
    }

    public function rules(): array
    {
        return [
            [['report_name', 'user_id'], 'required'],
            [['user_id'], 'integer'],
            [['report_name'], 'string', 'max' => 255],
            [['fields_visible'], 'safe'],
        ];
    }
}