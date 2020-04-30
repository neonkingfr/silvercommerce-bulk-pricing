<?php

namespace SilverCommerce\BulkPricing\Model;

use SilverStripe\i18n\i18n;
use SilverStripe\ORM\DataObject;
use SilverStripe\Control\HTTPRequest;
use SilverStripe\Core\Injector\Injector;
use SilverCommerce\TaxAdmin\Model\TaxRate;
use SilverCommerce\TaxAdmin\Traits\Taxable;
use SilverCommerce\TaxAdmin\Interfaces\TaxableProvider;
use SilverCommerce\CatalogueAdmin\Model\CatalogueProduct;
use SilverCommerce\BulkPricing\Tasks\MigrateLegacyBracketsTask;

/**
 * @method BulkPricingGroup Group
 * @method CatalogueProduct Product
 */
class BulkPricingBracket extends DataObject implements TaxableProvider
{
    use Taxable;

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

    /**
     * Temporary product that can be set to calculate nice prices (in template)
     *
     * @var null|CatalogueProduct
     */
    protected $temp_product = null;

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
     * Return a possible product for this bracket
     *
     * @return null|CatalogueProduct
     */
    public function getPossibleProduct()
    {
        $product = null;

        if ($this->Product()->exists()) {
            $product = $this->Product();
        }

        if (empty($product)) {
            $product = $this->getTempProduct();
        }

        return $product;
    }

    /**
     * Return price suitable for Taxable
     *
     * @return float
     */
    public function getBasePrice()
    {
        $product = $this->getPossibleProduct();

        if (!empty($product)) {
            return $this->modifyPrice($product->getBasePrice());
        }

        return (float) $this->Price;
    }

    /**
     * Return a Tax Percentage for the current product (if available)
     *
     * @return \SilverCommerce\TaxAdmin\Model\TaxRate
     */
    public function getTaxRate()
    {
        $product = $this->getPossibleProduct();

        if (!empty($product)) {
            return $product->getTaxRate();
        }

        $rate = TaxRate::create();
        $rate->ID = -1;

        return $rate;
    }

    /**
     * Provide a string based locale for the current object (EG 'en_GB')
     *
     * @return string
     */
    public function getLocale()
    {
        return i18n::get_locale();
    }

    /**
     * Should we automatically show the price including TAX?
     *
     * @return bool
     */
    public function getShowPriceWithTax()
    {
        $product = $this->getPossibleProduct();

        if (!empty($product)) {
            return $product->getShowPriceWithTax();
        }

        return false;
    }

    /**
     * Should we add a "Tax String" (EG "Includes VAT") to the rendered currency?
     *
     * @return bool|null
     */
    public function getShowTaxString()
    {
        $product = $this->getPossibleProduct();

        if (!empty($product)) {
            return $product->getShowTaxString();
        }

        return false;
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
     * @return  null|CatalogueProduct
     */
    public function getTempProduct()
    {
        return $this->temp_product;
    }

    /**
     * @param CatalogueProduct $temp_product
     *
     * @return self
     */
    public function setTempProduct(CatalogueProduct $product)
    {
        $this->temp_product = $product;
        return $this;
    }
}
