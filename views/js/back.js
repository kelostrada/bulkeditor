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

$(document).ready(() => {
    $('#bulk-editor-products').DataTable({paging: false});

    $('#bulk-editor-categories').on('change', () => {
        $('#bulk-editor-categories').submit();
    });

    $('#bulk-editor-products').on('blur', '.update-price', (e) => {
        const id = $(e.target).data('id');
        const value = $(e.target).val();
        const link = $(e.delegateTarget).data('link');
        console.log(id, value, link);

        $.ajax({
            type: 'POST',
            cache: false,
            dataType: 'json',
            url: link,
            data: {
                ajax: true,
                controller: 'AdminBulkEditor',
                action: 'SavePrice',
                id: id,
                price: value
            },
            success: function (data) {
                console.log("success", data);
            }
        });
    });

    $('#bulk-editor-products').on('blur', '.update-quantity', (e) => {
        const id = $(e.target).data('id');
        const value = $(e.target).val();
        const link = $(e.delegateTarget).data('link');
        console.log(id, value, link);

        $.ajax({
            type: 'POST',
            cache: false,
            dataType: 'json',
            url: link,
            data: {
                ajax: true,
                controller: 'AdminBulkEditor',
                action: 'SaveQuantity',
                id: id,
                quantity: value
            },
            success: function (data) {
                console.log("success", data);
            }
        });
    });
});

