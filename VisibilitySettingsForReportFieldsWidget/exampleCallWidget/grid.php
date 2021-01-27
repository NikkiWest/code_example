<?php

/* @var $this View */
/* @var $searchModel ClientsSearch */

/* @var $dataProvider ArrayDataProvider */

use app\lib\helpers\ReportFieldsSettingHelper;
use app\models\search\directory\ClientsSearch;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

$headers = $searchModel->getHeader();
$visibleFields = ReportFieldsSettingHelper::getSettingsForReport('supplier', $headers);

try {
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'showOnEmpty' => false,
        'tableOptions' => ['class' => 'table table-striped table-bordered scroll table-report table-directory'],
        'emptyTextOptions' => ['class' => 'empty-text'],
        'emptyText' => 'Данных не найдено',
        'layout' => "\n{items}\n <div class='group-summary-pager'>{pager} {summary}</div>",
        'columns' => [
            ['class' => 'yii\grid\SerialColumn',],
            ['class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function ($model) {
                    return ["data-client_id" => $model->client_id, "data-supplier_id" => $model->supplier_id];
                },
                'header' => Html::checkBox('selection_all', false, [
                    'class' => 'select-on-check-all',
                ]),
            ],
            ['label' => $headers['client_id'], 'attribute' => 'client_id',
                'visible' => in_array('client_id', $visibleFields)
            ],
            [
                'label' => $headers['client_name'],
                'visible' => in_array('client_name', $visibleFields),
                'attribute' => 'client_name',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->client_name, '/directory/clients?clientId=' . $data->client_id);
                }
            ],
            ['label' => $headers['supplier_id'], 'attribute' => 'supplier_id',
                'visible' => in_array('supplier_id', $visibleFields)
            ],
            ['label' => $headers['supplier_name'], 'attribute' => 'supplier_name',
                'visible' => in_array('supplier_name', $visibleFields)
            ],
            ['label' => $headers['brand_cnt'],
                'attribute' => 'brand_cnt',
                'visible' => in_array('brand_cnt', $visibleFields),
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->brand_cnt,
                        '/directory/brand?clientId=' . $data->client_id);
                }
            ],
            ['label' => "Ширина ассортимента (активный / весь) (шт)",
                'format' => 'raw',
                'visible' => (in_array('product_all_cnt', $visibleFields) ||
                    in_array('product_activity_cnt', $visibleFields)),
                'value' => function ($data) {
                    return Html::a($data->product_all_cnt . "/" . $data->product_activity_cnt,
                        '/directory/product?clientId=' . $data->client_id);
                }
            ],
            ['label' => $headers['minsum'], 'attribute' => 'minsum'],
            ['label' => $headers['periodicity'], 'attribute' => 'periodicity'],
        ]
    ]);
} catch (Exception $e) {
    echo $e->getMessage();
    echo $e->getTraceAsString();
}
