<?php

namespace SilverCommerce\BulkPricing\Model;

use SilverStripe\ORM\DataObject;
use SilverCommerce\BulkPricing\Model\BulkPricingBracket;
use SilverCommerce\CatalogueAdmin\Model\CatalogueProduct;
use SilverCommerce\CatalogueAdmin\Model\CatalogueCategory;
use SilverCommerce\BulkPricing\Model\GroupBulkPricingBracket;

/**
 * @method CatalogueCategory Category
 * @method \SilverStripe\ORM\HasManyList PricingBrackets
 */
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
        'PricingBrackets' => BulkPricingBracket::class,
        'Brackets' => GroupBulkPricingBracket::class // Legacy, soon to be removed
    ];

    /**
     * Compile full list of products in this group
     *
     * @return \SilverStripe\ORM\SS_List
     */
    public function getValidProducts()
    {
        $ids = array_merge(
            $this->Products()->column("ID"),
            $this->Category()->AllProducts()->column('ID')
        );

        $products = CatalogueProduct::get()->filter("ID", $ids);

        return $products;
    }
}
