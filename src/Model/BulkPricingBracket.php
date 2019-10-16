<?php

namespace SilverCommerce\BulkPricing\Model;

use SilverStripe\ORM\DataObject;
use SilverCommerce\TaxAdmin\Helpers\MathsHelper;
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

    private static $casting = [
        "TaxAmount"             => "Currency",
        "PriceAndTax"           => "Currency"
    ];
}