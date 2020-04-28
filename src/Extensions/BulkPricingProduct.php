<?php

namespace SilverCommerce\BulkPricing\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\GridField\GridField;
use SilverShop\HasOneField\HasOneButtonField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverCommerce\BulkPricing\Model\BulkPricingGroup;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverCommerce\BulkPricing\Model\BulkPricingBracket;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\ORM\ArrayList;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;

/**
 * @method BulkPricingGroup PricingGroup
 * @method \SilverStripe\ORM\HasManyList PricingBrackets
 */
class BulkPricingProduct extends DataExtension
{
    private static $has_one = [
        'PricingGroup' => BulkPricingGroup::class
    ];

    private static $has_many = [
        'PricingBrackets' => BulkPricingBracket::class
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $config = GridFieldConfig::create()
            ->addComponent(new GridFieldButtonRow('before'))
            ->addComponent(new GridFieldToolbarHeader())
            ->addComponent(new GridFieldTitleHeader())
            ->addComponent(new GridFieldEditableColumns())
            ->addComponent(new GridFieldDeleteAction())
            ->addComponent(new GridFieldAddNewInlineButton());

        $fields->addFieldsToTab(
            'Root.BulkPricing',
            [
                HasOneButtonField::create(
                    $this->getOwner(),
                    'PricingGroup'
                ),
                GridField::create(
                    'PricingBrackets',
                    'Bulk Prices',
                    $this->owner->PricingBrackets()
                )->setConfig($config)
            ]
        );

        $fields->removeByName('PricingBrackets');
    }

    /**
     * Get a list of valid pricing brackets for this product. If a quantity is provided,
     * then can also filter by that.
     *
     * @return \SilverStripe\ORM\SS_List
     */
    public function getValidPricingBrackets($qty = null)
    {
        $filter = [];
        $brackets = null;
        $owner = $this->getOwner();

        if (is_int($qty)) {
            $filter['MinQTY:LessThanOrEqual'] = $qty;
            $filter['MaxQTY:GreaterThanOrEqual'] = $qty;
        }

        // Does this product have a bulk price directly assigned?
        if (count($filter)) {
            $brackets = $owner->PricingBrackets()->filter($filter);
        } else {
            $brackets = $owner->PricingBrackets();
        }

        // If no direct prices assigned, first look via a group
        if (!$brackets->exists()) {
            if (count($filter)) {
                $brackets = $owner->PricingGroup()->PricingBrackets()->filter($filter);
            } else {
                $brackets = $owner->PricingGroup()->PricingBrackets();
            }
        }

        // Finally, if no prices from group, look for groups from categories
        if (!$brackets->exists()) {
            $categories = $owner->Categories();
            $category_ids = [];

            foreach ($categories as $category) {
                $category_ids = array_merge(
                    $category_ids,
                    $category->getAncestors(true)->column('ID')
                );
            }

            if (count($category_ids)) {
                $filter['Group.Category.ID'] = $category_ids;
            }

            if (count($filter)) {
                $brackets = BulkPricingBracket::get()->filter($filter);
            }
        }

        if (empty($brackets)) {
            $brackets = ArrayList::create();
        }

        return $brackets;
    }

    /**
     * Fetch the correct base price based on the provided quantity.
     *
     * This method follows a priority system, directly assigned prices
     * are checked first, then prices linked via a group, finally prices
     * linked via a category
     *
     * @param int $qty
     *
     * @return float
     */
    public function getBulkPrice(int $qty)
    {
        $owner = $this->getOwner();
        $price = $owner->getBasePrice();
        $brackets = $this->getValidPricingBrackets($qty);

        if ($brackets->exists()) {
            /** @var BulkPricingBracket */
            $bracket = $brackets->first();
            $price = $bracket->modifyPrice($price);
        }

        return $price;
    }

    /**
     * Get a rendered pricing table for this Product
     *
     * @return string
     */
    public function getPricingTable()
    {
        return $this->getOwner()->renderWith(
            'ilateral\\SilverStripe\\BulkPricing\\Includes\\PricingTable'
        );
    }
}
