<?php
/** @var WarehouseSearch $searchModel */

use app\widgets\VisibilitySettingsForReportFieldsWidget\VisibilitySettingsForReportFields;

try {
    echo VisibilitySettingsForReportFields::widget(['reportName' => 'supplier', 'fields' => $searchModel->getHeader()]);
} catch (Exception $e) {
    echo $e->getMessage();
}