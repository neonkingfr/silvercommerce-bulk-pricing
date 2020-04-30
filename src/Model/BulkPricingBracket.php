<?php

namespace SilverCommerce\BulkPricing\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\FieldType\DBCurrency;
use SilverCommerce\CatalogueAdmin\Model\CatalogueProduct;
use SilverCommerce\BulkPricing\Tasks\MigrateLegacyBracketsTask;

/**
 * @method BulkPricingGroup Group
 * @method CatalogueProduct Product
 */
class BulkPricingBracket extends DataObject
{
    private static $table_name = 'BulkPricingBracket';

    /**
     * Should bulk pricing brackets allow negative numbers?
     * Defaults to false, if a negative number appears, it is
     * set to 0
     *
     * @var boolean
     * @config
     */
    private static $allow_negative = false;

    private static $db = [
        'Quantity' => 'Int', // Legacy, will be removed
        'MinQTY' => 'Int',
        'MaxQTY' => 'Int',
        'Price' => 'Currency',
        'Reduce' => 'Boolean'
    ];

    private static $has_one = [
        'Product' => CatalogueProduct::class,
        'Group' => BulkPricingGroup::class
    ];

    private static $summary_fields = [
        'MinQTY',
        'MaxQTY',
        'Price',
        'Reduce'
    ];

    private static $field_labels = [
        'Reduce' => 'Reduce Product Price?'
    ];

    private static $default_sort = [
        'MinQTY' => 'ASC'
    ];

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        $run_migration = MigrateLegacyBracketsTask::config()->run_during_dev_build;

        if ($run_migration) {
            $request = Injector::inst()->get(HTTPRequest::class);
            MigrateLegacyBracketsTask::create()->run($request);
        }
    }

    /**
     * Modify a provided price based on the current bracket settings
     *
     * @param float $price
     *
     * @return float
     */
    public function modifyPrice(float $price)
    {
        $allow_negative = $this->config()->allow_negative;
        $price = ($this->Reduce) ? $price - $this->Price : $this->Price;

        if (!$allow_negative && $price < 0) {
            return 0;
        } else {
            return $price;
        }
    }

    /**
     * Get the Total price and tax
     *
     * @return float
     */
    public function getPriceAndTax()
    {
        $notax = $this->Price;
        if ($this->ProductID) {
            $product = $this->Product();
            $percent = $product->getTaxPercentage();

            // Round using default rounding defined on MathsHelper
            $tax = ($notax / 100) * $percent;
            $price = $notax + $tax;

            return DBCurrency::create()->setValue($price);
        }

        return $notax;
    }
}
