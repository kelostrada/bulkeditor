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

    public function ajaxProcessSaveReference()
    {
        $id = Tools::getValue("id");
        $reference = Tools::getValue("value");

        $product = new Product((int)$id, false, $this->context->language->id);
        $product->reference = $reference;
        if ($product->save()) 
        {
            echo json_encode(array('result' => 'success'));
        }
        else
        {
            echo json_encode(array('result' => 'error'));
        }
    }

    public function ajaxProcessSaveQuantity()
    {
        $id = Tools::getValue("id");
        $quantity = Tools::getValue("value");

        $product = new Product((int)$id, false, $this->context->language->id);
        $product->quantity = (int)$quantity;
        if ($product->save()) 
        {
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
        $price = Tools::getValue("value");

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

    public function ajaxProcessToggleActive()
    {
        $id = Tools::getValue("id");
        $product = new Product((int)$id, false, $this->context->language->id);
        $product->active = !$product->active;

        if ($product->save())
        {
            echo json_encode(array('result' => 'success', 'active' => $product->active));
        }
        else
        {
            echo json_encode(array('result' => 'error'));
        }
    }

    protected function getProducts()
    {
        $category = new Category($this->categoryID, $this->context->language->id);
        $products = $category->getProducts($this->context->language->id, 0, 10000, null, null, false, false, false, 1, false);

        if (!$products) return [];

        foreach($products as &$product) {
            $images = Product::getCover($product["id_product"]);
            $product["cover"] = $images ? $images["id_image"] : null;
        }

        return $products;
    }

    protected function getFeatures()
    {
        $features = [];
        $products = $this->getProducts();

        foreach($products as $product)
        {
            foreach($product["features"] as $productFeature)
            {
                if (!$features[$productFeature["id_feature"]]) $features[$productFeature["id_feature"]] = [];
                $features[$productFeature["id_feature"]]["id_feature"] = $productFeature["id_feature"];
                $features[$productFeature["id_feature"]]["name"] = $productFeature["name"];
                $features[$productFeature["id_feature"]]["values"][$productFeature["value"]] = ["value" => $productFeature["value"]];
            }
        }

        foreach($features as $id => &$feature)
        {
            $values = FeatureValue::getFeatureValuesWithLang($this->context->language->id, $id);

            foreach($feature["values"] as &$value)
            {
                $value_key = array_search($value["value"], array_column($values, "value"));
                $value["id_feature_value"] = $values[$value_key]["id_feature_value"];
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
            $passingFeatures = [];

            foreach($features as $feature)
            {
                // check if the values were even selected
                $passingFeatures[$feature["id_feature"]] = array_reduce($feature["values"], function($acc, $value) {
                    return $acc && !$value["selected"];
                }, true);

                if ($passingFeatures[$feature["id_feature"]]) continue;

                foreach($feature["values"] as $value)
                {
                    if (!$value["selected"]) continue;

                    foreach($product["features"] as $product_feature)
                    {
                        if ($product_feature["name"] == $feature["name"] && $product_feature["value"] == $value["value"])
                        {
                            $passingFeatures[$feature["id_feature"]] = true;
                            continue;
                        }
                    }
                }
            }

            return array_reduce($passingFeatures, function($acc, $pass) {
                return $acc && $pass;
            }, true);
        });
    }
    
}