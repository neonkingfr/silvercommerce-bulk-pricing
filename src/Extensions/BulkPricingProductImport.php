<?php

namespace SilverCommerce\BulkPricing\Extensions;

use SilverCommerce\BulkPricing\Model\BulkPricingBracket;
use SilverStripe\Core\Extension;

class BulkPricingProductImport extends Extension
{
    public function onAfterProcess($object, $record, $columnMap, $results, $preview)
    {
        if (!empty($object) && isset($record['PricingBracketsList'])) {
            $items = explode(',', $record['PricingBracketsList']);
            $object->PricingBrackets()->removeAll();

            foreach ($items as $item) {
                $bracket = BulkPricingBracket::create();
                $bracket->ProductID = $object->ID;
                $item = trim($item);
                $item_parts = explode(';', $item);

                foreach ($item_parts as $part) {
                    $parts = explode(":", $part);
                    if (isset($parts[0]) && isset($parts[1])) {
                        $bracket->{$parts[0]} = $parts[1];
                    }
                }

                $bracket->write();
            }
        }
    }
}
