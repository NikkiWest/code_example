/**
 * Класс по работе с дозаказом, выбор продуктов из грида и их отправка на сервер для сохранения
 */
class SupplyOrder {
    yiiGridView;

    static async setHandlers() {
        $('.create-order').on('click', async function () {
            await SupplyOrder.form();
        });
    }

    static async form(callbackOnUpdate) {
        let grid = $('.grid-view');
        if (typeof grid.yiiGridView !== 'function') {
            ModalBox.error('Произведите расчёт и выберите позиции для дозаказа');
            return false;
        }
        let ids = grid.yiiGridView('getSelectedRows'),
            orders = [];
        $('.product-to-order-count').each(function () {
            let id = $(this).data('product-id');
            if (ids.includes(id)) {
                orders.push({
                    id: id,
                    name: $(this).data('name'),
                    products: $(this).val(),
                    price: $(this).data('cost')
                });
            }
        });
        if (orders.length === 0) {
            ModalBox.error('Не выбраны позиции для дозаказа');
            return false;
        }

        let modal = new ModalBox({
            idPrefix: 'lt-start-',
            title: 'Создание дозаказа',
            size: 'modal-lg'
        });
        modal.body.html('Loading...').html(await $.post('/supply/order/form', {orders: orders}));
        $('#order-form').on('submit', function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();
            let emptyValue = [];
            let allCntProduct = [];
            $('.product-order').each(function () {
                if (parseInt($(this).val()) === 0) {
                    emptyValue.push({
                        product_id: $(this).data('product-id'),
                        product_name: $(this).data('product-name')
                    });
                }
                allCntProduct.push($(this).data('product-id'));
            });

            if (emptyValue.length > 0) {
                let errorText = '<ul>Заполните кол-во для продуктов:';
                jQuery.each(emptyValue, function (i, val) {
                    errorText += '<li>' + val.product_name + "</li>";
                });
                ModalBox.error(errorText + '</ul>');
                return false;
            } else {
                if (allCntProduct.length === 0) {
                    ModalBox.error('Не выбраны позиции для дозаказа');
                    return false;
                } else {
                    const message = 'Подтвердите запуск процесса.<br>' +
                        'При этом будут выполнены запросы к МП, после чего будут обновлены данные в нашей БД.<br>' +
                        'Если существовали данные по выбранным критериям, то возможно они будут удалены';
                    const formName = 'SupplyOrderForm';
                    ModalBox.confirm(message, async function () {
                        let formData = new FormData();
                        formData.append(formName + '[clientId]', $('#supply-calc-filter-form #clientId').val());
                        formData.append(formName + '[marketplaceId]', $('#supply-calc-filter-form #marketplaceId').val());
                        formData.append(formName + '[warehouseId]', $('#supply-calc-filter-form #warehouseId').val());
                        formData.append(formName + '[brandId]', $('#supply-calc-filter-form #brandIds').val());
                        await SupplyOrder._save(formName, formData, async function (response) {
                            modal.close();
                            ModalBox.success(response.message);
                            if (callbackOnUpdate) {
                                await callbackOnUpdate();
                            }
                        });
                    });
                }
            }
            return false;
        });
        $(".js-RemoveProductFromOrder").on("click", function (e) {
            e.preventDefault();
            let productId = $(this).data("product_id");
            let productAmount = $(this).data("product_amount");
            let totalOrderAmountSelector = $("#totalOrderAmount");
            let totalOrderAmount = totalOrderAmountSelector.html();
            let totalOrderAmountNew = Number(totalOrderAmount) - Number(productAmount);
            totalOrderAmountSelector.html(totalOrderAmountNew.toFixed(1));
            $(this).parent().remove();
            $("#checkbox-" + productId).click();
        });
    }

    /**
     * @param {String} formName
     * @param {FormData} formData
     * @param {function} onSuccess, optional
     * @returns {Promise<void>}
     * @private
     */
    static async _save(formName, formData, onSuccess) {
        formData.append(
            $('meta[name="csrf-param"]').attr('content'),
            $('meta[name="csrf-token"]').attr('content')
        );
        let confirmedOrders = [];
        $('.product-order').each(function () {
            confirmedOrders.push({
                id: $(this).data('product-id'),
                name: $(this).data('product-name'),
                productCount: parseInt($(this).val()),
                price: $(this).data('cost-price')
            });
        });
        formData.append(formName + '[order]', JSON.stringify(confirmedOrders));
        try {
            const response = await $.ajax({
                url: '/supply/order/save',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false
            });
            if (response && response['success']) {
                if (onSuccess) {
                    onSuccess(response);
                }
            } else {
                ModalBox.error(response && response['message'] ? response['message'] : 'Internal error');
            }
        } catch (error) {
            ModalBox.error(error);
        }
    }
}
