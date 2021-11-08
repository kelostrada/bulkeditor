<div class="panel">
	<div class="panel-heading">
        {l s='Bulk Editor' mod='bulkeditor'}
    </div>
    
    <div class="panel-body" id="bulk-editor">

        <div class="container">
            <form class="form form-inline" role="form" id="bulk-editor-categories" type="GET">
                <input type="hidden" name="controller" value="AdminBulkEditor" />
                <input type="hidden" name="token" value="{Tools::getAdminTokenLite('AdminBulkEditor')}" />

                <select class="form-control" id="bulk-editor-category" name="category_id">
                    <option value="">--- SELECT CATEGORY ---</option>
                    {foreach from=$categories item=category}
                    <option value="{$category.id}" {if $category.id == $categoryID}selected{/if}>{$category.name}</option>
                    {/foreach}
                </select>

                <hr/>

                {foreach from=$features item=feature}
                <select class="form-control" id="bulk-editor-feature-{$feature.name}" name="features[{$feature.id_feature}][]" multiple>
                    {foreach from=$feature.values item=value}
                    <option value="{$value.id_feature_value}" {if $value.selected}selected{/if}>{$value.value}</option>
                    {/foreach}
                </select>
                {/foreach}
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
                        <th>Features</th>
                        <th>Active</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$products item=product}
                    <tr>
                        <td><img height="80" src="{$link->getImageLink($product.link_rewrite, $product.cover, '')}"></td>
                        <td>
                            <a target="_blank" href="{Context::getContext()->link->getAdminLink('AdminProducts', true, ['id_product' => $product.id_product])}">
                                {$product["name"]}
                            </a>
                        </td>
                        <td>
                            <span>{$product.reference}</span>
                        </td>
                        <td>
                            <div class="form-group has-feedback">
                                <input type="text" class="form-control update-price" placeholder="Enter price" 
                                        value="{$product.price}" data-id="{$product.id_product}" size="5">
                                <span class="glyphicon form-control-feedback"></span>
                            </div>
                        </td>
                        <td>
                            <div class="form-group has-feedback">
                                <input type="text" class="form-control update-quantity" placeholder="Enter quantity" 
                                        value="{$product.quantity}" data-id="{$product.id_product}" size="5">
                                <span class="glyphicon form-control-feedback"></span>
                            </div>
                        </td>
                        <td>
                            <ul>
                                {foreach from=$product.features item=feature}
                                <li>{$feature.name}: {$feature.value}</li>
                                {/foreach}
                            </ul>
                        </td>
                        <td>
                            <span class="toggle-active" data-id="{$product.id_product}">
                                {if $product.active}
                                <i class="material-icons">check</i>
                                {else}
                                <i class="material-icons">clear</i>
                                {/if}
                            </span>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>
