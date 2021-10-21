/**
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/

function updateValues(event, action) {
    const id = $(event.target).data('id');
    const value = $(event.target).val();
    const link = $(event.delegateTarget).data('link');
    const $formGroup = $(event.target).parent('.form-group');
    const $glyphicon = $(event.target).siblings('.glyphicon');

    $formGroup.addClass("has-warning");
    $glyphicon.addClass("glyphicon-refresh spinner");

    setTimeout(function() {
        $formGroup.removeClass("has-success has-warning has-error");
        $glyphicon.removeClass("glyphicon-ok glyphicon-remove glyphicon-refresh spinner");
    }, 5000);

    $.ajax({
        type: 'POST',
        cache: false,
        dataType: 'json',
        url: link,
        timeout: 3000,
        data: {
            ajax: true,
            controller: 'AdminBulkEditor',
            action: action,
            id: id,
            value: value
        },
        success: function (data) {
            $formGroup.removeClass("has-warning");
            $glyphicon.removeClass("glyphicon-refresh spinner");

            if (data.result == "success") {
                $formGroup.addClass("has-success");
                $glyphicon.addClass("glyphicon-ok");
            } else {
                console.error("error while saving: ", data);

                $formGroup.addClass("has-error");
                $glyphicon.addClass("glyphicon-remove");
            }
        },
        error: function(data) {
            console.error("error while saving: ", data);

            $formGroup.removeClass("has-warning");
            $glyphicon.removeClass("glyphicon-refresh spinner");
            $formGroup.addClass("has-error");
            $glyphicon.addClass("glyphicon-remove");
        }
    });
}

$(document).ready(() => {
    $('#bulk-editor-products').DataTable({paging: false});

    $('#bulk-editor-categories').on('change', () => {
        $('#bulk-editor-categories').submit();
    });

    $('#bulk-editor-products').on('blur', '.update-price', (event) => {
        updateValues(event, 'SavePrice');
    });

    $('#bulk-editor-products').on('blur', '.update-quantity', (event) => {
        updateValues(event, 'SaveQuantity');
    });
});
