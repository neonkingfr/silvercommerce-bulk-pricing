<?php

namespace SilverCommerce\BulkPricing\Extensions;

use SilverStripe\ORM\DataExtension;
use SilverCommerce\BulkPricing\Model\BulkPricingGroup;

class BulkPricingLineItem extends DataExtension
{
    public function onBeforeWrite()
    {
        $owner = $this->getOwner();

        // Check the StockItem for BulkPriceBrackets
        $product = $owner->FindStockItem();

        // If BulkPriceBrackets exist - ensure current Price is set correctly
        if (!empty($product)) {
            $owner->BasePrice = $product->getBulkPrice($owner->Quantity);
        }
    }
}
