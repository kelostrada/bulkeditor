<?php

class AdminBulkEditorController extends ModuleAdminController 
{
    protected $categoryID = false;

    public function __construct() 
    {
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $this->bootstrap = true;
        $this->categoryID = Tools::getValue("category_id");
    }

    public function initContent()
    {
        $assigns = array(
            'products' => $this->getProducts(),
            'categories' => $this->getCategories(),
            'categoryID' => $this->categoryID
        );

        parent::initContent();
        $this->context->smarty->assign($assigns);
        $this->setTemplate('bulk_editor.tpl');
    }

    public function ajaxProcessSaveQuantity()
    {
        $id = Tools::getValue("id");
        $quantity = Tools::getValue("quantity");

        $product = new Product((int)$id, false, $this->context->language->id);
        $product->quantity = (int)$quantity;
        if ($product->save()) {
            StockAvailable::setQuantity((int)$product->id, 0, $product->quantity, $this->context->shop->id);
            echo json_encode(array('result' => 'success'));
        }
        else
        {
            echo json_encode(array('result' => 'error'));
        }
    }

    public function ajaxProcessSavePrice()
    {
        $id = Tools::getValue("id");
        $price = Tools::getValue("price");

        $product = new Product((int)$id, false, $this->context->language->id);
        $tax = 1 + $product->getTaxesRate() / 100.0;
        $product->price = strval(round(floatval($price) / $tax, 6));

        if ($product->save())
        {
            echo json_encode(array('result' => 'success'));
        } 
        else
        {
            echo json_encode(array('result' => 'error'));
        }
    }

    protected function getProducts()
    {
        $category = new Category($this->categoryID, $this->context->language->id);
        $products = $category->getProducts($this->context->language->id, 0, 10000, null, null, false, false);

        if (!$products) return [];

        foreach($products as &$product) {
            $images = Product::getCover($product["id_product"]);
            $product["cover"] = $images["id_image"];
        }

        return $products;
    }

    protected function getCategories()
    {
        $categories = Category::getCategories( (int)($this->context->language->id), true, false );

        $categories = array_map(function($category) {
            return ['id' => $category["id_category"], 'name' => $category["name"]];
        }, $categories);

        return $categories;
    }
    
}