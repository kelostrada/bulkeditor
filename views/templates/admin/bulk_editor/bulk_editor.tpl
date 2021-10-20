<div class="panel">
	<div class="panel-heading">
        {l s='Bulk Editor' mod='bulkeditor'}
    </div>
    
    <div class="panel-body" id="bulk-editor">

        <div class="container">
            <form class="form" role="form" id="bulk-editor-categories" type="GET">
                <input type="hidden" name="controller" value="AdminBulkEditor" />
                <input type="hidden" name="token" value="{Tools::getAdminTokenLite('AdminBulkEditor')}" />

                <select class="form-control" id="bulk-editor-category" name="category_id">
                    <option value="">--- SELECT CATEGORY ---</option>
                    {foreach from=$categories item=category}
                    <option value="{$category.id}" {if $category.id == $categoryID}selected{/if}>{$category.name}</option>
                    {/foreach}
                </select>
            </form>
        </div>

        <br/>

        <div>
            <table class="table" id="bulk-editor-products" data-link="{$link->getAdminLink( 'AdminBulkEditor' )}">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Reference</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Active</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$products item=product}
                    <tr>
                        <td><img height="50" src="{$link->getImageLink($product.link_rewrite, $product.cover, '')}"></td>
                        <td>{$product["name"]}</td>
                        <td>{$product["reference"]}</td>
                        <td>
                            <input type="text" class="form-control update-price" placeholder="Enter price" 
                                    value="{$product.price}" data-id="{$product.id_product}">
                        </td>
                        <td>
                            <input type="text" class="form-control update-quantity" placeholder="Enter quantity" 
                                    value="{$product.quantity}" data-id="{$product.id_product}">
                        </td>
                        <td>
                        {if $product.active}
                        <i class="material-icons action-enabled">check</i>
                        {else}
                        <i class="material-icons action-disabled">clear</i>
                        {/if}
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>
