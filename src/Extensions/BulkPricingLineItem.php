<?php

namespace SilverCommerce\BulkPricing\Extensions;

use SilverStripe\ORM\DataExtension;
use SilverCommerce\BulkPricing\Model\BulkPricingGroup;

class BulkPricingLineItem extends DataExtension
{
    public function onBeforeWrite()
    {
        // If Quantity has been changed
        if ($this->owner->isChanged('Quantity') || !$this->owner->ID) {
            // Check the StockItem for BulkPriceBrackets
            $product = $this->owner->findStockItem();
            
            // If BulkPriceBrackets exist - ensure current Price is set correctly
            if ($product) {
                $this->owner->Price = $product->getBulkPrice($this->owner->Quantity);
            }
        }
    }

    /**
     * Check if this LineItem is in a BulkPricingGroup
     *
     * @param BulkPricingGroup $group
     * @return boolean
     */
    public function isInPricingGroup(BulkPricingGroup $group)
    {
        $product = $this->FindStockItem();
        $group_products = $group->getValidProducts();

        if ($group_products->contains($product)) {
            return true;
        }

        return false;
    }
}