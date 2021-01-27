<?php
/**
 * Created by PhpStorm.
 * User: nikki
 * Date: 21.10.2020
 * Time: 11:18
 */

namespace console\controllers;


use common\components\OpenExchangeRatesApi;
use common\models\Currency;
use yii\console\Controller;
use yii\db\Expression;

class CurrencyController extends Controller
{
    /**
     * Получение списка валют за сегодняшний день
     */
    public function actionGetCurrency()
    {
        $OpenExchangeRatesApi = new OpenExchangeRatesApi();
        $currencyList = $OpenExchangeRatesApi->getLatest();
        if (!is_null($currencyList)) {
            $baseCurrency = $currencyList['base'];
            foreach ($currencyList['rates'] as $currency => $value) {
                $Currency = Currency::find()->where(['name' => $currency])
                    ->andWhere(new Expression('DATE(date_update) = :current_date',
                        [':current_date' => date('Y-m-d')]))->one();
                if (empty($Currency)) {
                    $Currency = new Currency();
                    $Currency->name = $currency;
                    $Currency->base_currency = $baseCurrency;
                }
                $Currency->value = $value;
                $Currency->date_update = date('Y-m-d H:i:s');
                $Currency->save();
            }
        }
    }
}