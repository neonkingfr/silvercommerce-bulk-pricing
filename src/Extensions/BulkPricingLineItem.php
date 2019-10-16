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
        if ($product) {
            $owner->BasePrice = $product->getBulkPrice($owner->Parent(), $owner->ID, $owner->Quantity);
        }
    }

    public function onAfterWrite()
    {
        $owner = $this->getOwner();

        if ($owner->isChanged('Quantity')) {
            $parent = $owner->Parent();
            foreach ($parent->Items() as $item) {
                if ($item->ID != $owner->ID) {
                    $item->write();
                }
            }
        }
    }

    public function onAfterDelete()
    {
        $owner = $this->getOwner();

        $parent = $owner->Parent();
        foreach ($parent->Items() as $item) {
            if ($item->ID != $owner->ID) {
                $item->write();
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
        $product = $this->getOwner()->FindStockItem();
        $group_products = $group->getValidProducts();

        if (in_array($product->ID, $group_products->column('ID'))) {
            return true;
        }

        return false;
    }
}