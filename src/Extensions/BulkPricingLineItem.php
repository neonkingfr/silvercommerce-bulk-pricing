<?php

namespace SilverCommerce\BulkPricing\Extensions;

use SilverStripe\ORM\DataExtension;

class BulkPricingLineItem extends DataExtension
{
    /**
     * Check to see if the current item is applicable for bulk pricing and
     * update if needed.
     *
     * @return null
     */
    public function onBeforeWrite()
    {
        /** @var LineItem */
        $owner = $this->getOwner();

        // Check the StockItem for BulkPriceBrackets
        $product = $owner->FindStockItem();

        if (empty($product) || !$product->hasMethod('getBulkPrice')) {
            return;
        }

        $group = $product->getPricingGroup();
        $qty = $owner->Quantity;

        // If no pricing groups are set (or group is not GroupedPricing)
        if (!$group->exists() || !$group->GroupedPricing) {
            $owner->BasePrice = $product->getBulkPrice($qty);
            return;
        }

        // Collect all items on this order and determine any BulkPricingGroups
        // that are relevent
        foreach ($owner->Parent()->Items() as $item) {
            // Skip current item
            if ($item->ID == $owner->ID) {
                continue;
            }

            // Skip any invalid products
            $temp_product = $item->FindStockItem();
            if (empty($temp_product)) {
                continue;
            }

            // If the pricing group from the item matches the provided
            // current group, increment the quantity
            $temp_group = $temp_product->getPricingGroup();
            if ($group->ID == $temp_group->ID) {
                $qty = $qty + $item->Quantity;
            }
        }

        $owner->BasePrice = $product->getBulkPrice($qty);
        return;
    }

    /**
     * Re-write all order items to ensure they apply any grouped pricing
     * (set via `BulkPricingGroup::GroupedPricing`).
     *
     * @return null
     */
    public function onAfterWrite()
    {
        $owner = $this->getOwner();
        if ($owner->isChanged('Quantity')) {
            foreach ($owner->Parent()->Items() as $item) {
                if ($item->ID != $owner->ID) {
                    $item->write();
                }
            }
        }
    }

    /**
     * Re-write all order items to ensure they apply any grouped pricing
     * (set via `BulkPricingGroup::GroupedPricing`).
     *
     * @return null
     */
    public function onAfterDelete()
    {
        $owner = $this->getOwner();
        foreach ($owner->Parent()->Items() as $item) {
            if ($item->ID != $owner->ID) {
                $item->write();
            }
        }
    }
}
