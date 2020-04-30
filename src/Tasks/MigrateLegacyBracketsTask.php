<?php

namespace SilverCommerce\BulkPricing\Tasks;

use SilverStripe\Dev\BuildTask;
use SilverCommerce\BulkPricing\Model\BulkPricingBracket;
use SilverCommerce\BulkPricing\Model\GroupBulkPricingBracket;

/**
 * Loops through all pricing brackets and updates the quantites and prices to the
 * new format.
 *
 * Also converts all legacy `GroupBulkPricingBracket`s to standard `BulkPricingBracket`s
 *
 * @package SilverCommerce
 * @subpackage BulkPricing
 */
class MigrateLegacyBracketsTask extends BuildTask
{
    protected $title = 'Migrate Legacy Price Brackets';
    
    protected $description = 'Find all legacy brackets and update';

    private static $segment = 'MigrateLegacyBracketsTask';

    private static $run_during_dev_build = true;
    
    public function run($request)
    {
        // First update old items to use new bands
        $brackets = BulkPricingBracket::get()
            ->sort(
                [
                    'ProductID' => 'ASC',
                    'GroupID' => 'ASC',
                    'Quantity' => 'ASC'
                ]
            )->toArray();

        for ($i = 0; $i < count($brackets); $i++) {
            $bracket = $brackets[$i];
            $next_qty = 9999;

            if ((int)$bracket->Quantity == 0) {
                continue;
            }

            if (isset($brackets[$i+1]) && $brackets[$i+1]->Quantity > $bracket->Quantity) {
                $next_qty = $brackets[$i+1]->Quantity - 1;
            }

            $bracket->MinQTY = $bracket->Quantity;
            $bracket->MaxQTY = $next_qty;
            $bracket->Quantity = 0;
            $bracket->write();
        }

        $j = 0;

        // Move legacy Grouped Bracket models
        $brackets = GroupBulkPricingBracket::get()
            ->sort(
                [
                    'GroupID' => 'ASC',
                    'Quantity' => 'ASC'
                ]
            )->toArray();

        for ($j = 0; $j < count($brackets); $j++) {
            $bracket = $brackets[$j];
            $new_bracket = BulkPricingBracket::create();
            $next_qty = 9999;

            if ((int)$bracket->Quantity == 0) {
                continue;
            }

            if (isset($brackets[$j+1]) &&
                $brackets[$j+1]->GroupID == $bracket->GroupID &&
                $brackets[$j+1]->Quantity > 0
            ) {
                $next_qty = $brackets[$j+1]->Quantity - 1;
            }

            $new_bracket->MinQTY = $bracket->Quantity;
            $new_bracket->MaxQTY = $next_qty;
            $new_bracket->Price = $bracket->Price;
            $new_bracket->GroupID = $bracket->GroupID;
            $new_bracket->Reduce = 1;
            $new_bracket->write();

            $bracket->delete();
        }

        echo "Migrated {$i} Items and {$j} Group Brackets.\n";
    }
}
