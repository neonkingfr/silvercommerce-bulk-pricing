<?php

namespace SilverCommerce\BulkPricing\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBCurrency;
use SilverCommerce\TaxAdmin\Helpers\MathsHelper;
use SilverCommerce\BulkPricing\Model\BulkPricingGroup;
use SilverCommerce\CatalogueAdmin\Model\CatalogueProduct;

class BulkPricingBracket extends DataObject
{
    private static $table_name = 'BulkPricingBracket';

    private static $db = [
        'Quantity' => 'Int',
        'Price' => 'Currency'
    ];

    private static $has_one = [
        'Product' => CatalogueProduct::class
    ];

    private static $summary_fields = [
        'Quantity' => 'Starting Quantity',
        'Price' => 'New Price per Unit'
    ];

    private static $default_sort = [
        'Quantity' => 'ASC'
    ];

    /**
     * Get the Total price and tax
     *
     * @return float
     */
    public function getPriceAndTax()
    {
        $notax = $this->Price;
        if ($this->ProductID) {
            $product = $this->Product();
            $percent = $product->getTaxPercentage();

            // Round using default rounding defined on MathsHelper
            $tax = ($notax / 100) * $percent;
            $price = $notax + $tax;

            return DBCurrency::create()->setValue($price);
        }

        return $notax;
    }
}