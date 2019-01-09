<?php

namespace SilverCommerce\BulkPricing\Model;

use SilverStripe\ORM\DataObject;
use SilverCommerce\TaxAdmin\Helpers\MathsHelper;
use SilverCommerce\CatalogueAdmin\Model\CatalogueProduct;

class BulkPricingBracket extends DataObject
{
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

    private static $casting = [
        "TaxAmount"             => "Currency",
        "PriceAndTax"           => "Currency"
    ];

    /**
     * Get a final tax amount for this product. You can extend this
     * method using "UpdateTax" allowing third party modules to alter
     * tax amounts dynamically.
     * 
     * @return Float
     */
    public function getTaxAmount($decimal_size = null)
    {
        // Round using default rounding defined on MathsHelper
        $tax = MathsHelper::round(
            ($this->Price / 100) * $this->Product()->TaxRate,
            2
        );

        $this->extend("updateTaxAmount", $tax);

        return $tax;
    }
    
    /**
     * Get the final price of this product, including tax (if any)
     *
     * @return Float
     */
    public function getPriceAndTax()
    {
        $price = $this->Price + $this->TaxAmount;
        $this->extend("updatePriceAndTax", $price);

        return $price;
    }
}