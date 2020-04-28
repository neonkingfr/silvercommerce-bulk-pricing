<?php

namespace SilverCommerce\BulkPricing\Model;

use SilverStripe\ORM\DataObject;
use SilverCommerce\BulkPricing\Model\BulkPricingGroup;

/**
 * @depreciated all bulk prices will now be a type of BulkPricingBracket
 */
class GroupBulkPricingBracket extends DataObject
{
    private static $table_name = 'GroupBulkPricingBracket';

    private static $db = [
        'Quantity' => 'Int',
        'Price' => 'Currency'
    ];

    private static $has_one = [
        'Group' => BulkPricingGroup::class
    ];

    private static $summary_fields = [
        'Quantity' => 'Starting Quantity',
        'Price' => 'Reduce price by'
    ];

    private static $default_sort = [
        'Quantity' => 'ASC'
    ];

    private static $field_labels = [
        'Price' => 'Reduce price by'
    ];
}