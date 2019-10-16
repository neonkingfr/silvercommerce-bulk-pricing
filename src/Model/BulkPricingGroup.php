<?php

namespace SilverCommerce\BulkPricing\Model;

use SilverCommerce\CatalogueAdmin\Model\CatalogueCategory;
use SilverCommerce\CatalogueAdmin\Model\CatalogueProduct;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataObject;

class BulkPricingGroup extends DataObject
{
    private static $table_name = 'BulkPricingGroup';

    private static $db = [
        'Title' => 'Varchar'
    ];
    
    private static $has_one = [
        'Category' => CatalogueCategory::class,
    ];

    private static $has_many = [
        'Products' => CatalogueProduct::class,
        'Brackets' => BulkPricingBracket::class
    ];

    /**
     * Compile full list of products in this group
     *
     * @return ArrayList
     */
    public function getValidProducts()
    {
        $products = ArrayList::create();

        foreach ($this->Products() as $product) {
            $products->add($product);
        }

        if ($this->CategoryID && $category = $this->Category()) {
            foreach ($category->Products() as $product) {
                $products->add($product);
            }
        }

        return $products;
    }
}