<?php

namespace SilverCommerce\BulkPricing\Extensions;

use SilverStripe\ORM\DataExtension;

class BulkPricingLineItem extends DataExtension
{
    public function onBeforeWrite()
    {
        // If Quantity has been changed
        if ($this->owner->isChanged('Quantity')) {
            // Check the StockItem for BulkPriceBrackets
            $product = $this->owner->findStockItem();
            $brackets = false;

            if ($product) {
                $brackets = $product->PricingBrackets();
            }
            
            // If BulkPriceBrackets exist - ensure current Price is set correctly
            if ($brackets) {
                $this->owner->Price = $product->getBulkPrice($this->owner->Quantity);
            }
        }
    }
}