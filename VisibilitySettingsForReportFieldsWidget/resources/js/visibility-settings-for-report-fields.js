$('#form-settings-fields').on('beforeSubmit', function () {
    let data = $(this).serialize();
    let reportType = window.location.pathname.split("/")[1];
    $.ajax({
        url: '/site/save-setting-fields',
        type: 'POST',
        data: data,
        success: async function (response) {
            if (response && response.success) {
                ModalBox.success(response.message).closeAfter(2000);
                $("#modalSettingsColumns").modal("hide");
                switch (reportType) {
                    case 'directory':
                        await Directory.reload(Directory.getUrl());
                        break;
                    case 'supply':
                        await Calc.reload(Calc.getUrl());
                        break;
                    case 'orders':
                        await Demand.reload(Demand.getUrl());
                        break;
                    default:
                        location.reload();
                        break;
                }
                Utils.FixHead();
                return false;
            } else {
                ModalBox.error(response && response.message ? response.message : 'Internal error');
            }
        },
        error: function () {
            ModalBox.error('Internal error');
        }
    });
    return false;
});
