<?php

namespace SilverCommerce\BulkPricing\Extensions;

use SilverStripe\Core\Extension;

class BulkPricingAddToCartForm extends Extension
{
    public function updateAddItemToCart($item_to_add, $cart)
    {
        $model = $cart->findOrMake();
        foreach ($model->Items() as $item) {
            $item->write();
        }
    }
}