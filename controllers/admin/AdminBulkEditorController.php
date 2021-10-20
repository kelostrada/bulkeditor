<?php

class AdminBulkEditorController extends ModuleAdminController 
{
    protected $categoryID = false;
    protected $selectedFeatures = false;

    public function __construct() 
    {
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $this->bootstrap = true;
        $this->categoryID = Tools::getValue("category_id");
        $this->selectedFeatures = Tools::getValue("features");
    }

    public function initContent()
    {
        $features = $this->getFeatures();
        $products = $this->getProducts();
        if ($this->selectedFeatures)
        {
            $products = $this->filterProducts($products, $features);
        }
        $categories = $this->getCategories();

        $assigns = array(
            'products' => $products,
            'categories' => $categories,
            'features' => $features,
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

    protected function getFeatures()
    {
        $features = [];
        $products = $this->getProducts();

        foreach($products as $product)
        {
            $productFeatures = Product::getFeaturesStatic($product["id_product"]);

            foreach($productFeatures as $productFeature)
            {
                $features[$productFeature["id_feature"]] = null;
            }
        }

        foreach($features as $id => &$feature)
        {
            $feature = Feature::getFeature($this->context->language->id, $id);
            $feature["values"] = FeatureValue::getFeatureValuesWithLang($this->context->language->id, $id);

            foreach($feature["values"] as &$value)
            {
                $value["selected"] = !!in_array($value["id_feature_value"], $this->selectedFeatures[$id]);
            }
        }

        return $features;
    }

    protected function getCategories()
    {
        $categories = Category::getCategories( (int)($this->context->language->id), true, false );

        $categories = array_map(function($category) {
            return ['id' => $category["id_category"], 'name' => $category["name"]];
        }, $categories);

        return $categories;
    }

    private function filterProducts($products, $features)
    {
        return array_filter($products, function($product) use ($features) {
            foreach($features as $feature)
            {
                foreach($feature["values"] as $value)
                {
                    if (!$value["selected"]) continue;

                    foreach($product["features"] as $product_feature)
                    {
                        if ($product_feature["name"] == $feature["name"] && $product_feature["value"] == $value["value"])
                        {
                            return true;
                        }
                    }
                }
            }
            return false;
        });
    }
    
}