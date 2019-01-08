<?php

namespace SilverCommerce\BulkPricing\Extensions;

use SilverStripe\ORM\DataExtension;

class BulkPricingLineItem extends DataExtension
{
    public function onBeforeWrite()
    {
        // If hasChanged(Quantity)
        // Check the StockItem for BulkPriceBrackets
        // If BulkPriceBrackets exist - ensure current Price is set correctly
    }
}