<?php

namespace SilverCommerce\BulkPricing\Model;

class BulkPricingBracket extends DataObject
{
    private static $db = [
        'Quantity' => 'Int',
        'ChangeValue' => 'Currency'
    ];
}