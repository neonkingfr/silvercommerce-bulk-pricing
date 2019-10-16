<?php

namespace SilverCommerce\BulkPricing\Extensions;

use SilverStripe\ORM\ArrayList;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBCurrency;
use SilverStripe\Forms\GridField\GridField;
use SilverShop\HasOneField\HasOneButtonField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverCommerce\BulkPricing\Model\BulkPricingGroup;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverCommerce\BulkPricing\Model\BulkPricingBracket;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;

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
    }

    /**
     * fetch the correct base price based on the provided quantity
     *
     * @param Estimate $estimate
     * @return DBCurrency
     */
    public function getBulkPrice($estimate, $id, $new_qty)
    {

        $owner = $this->getOwner();
        $price = $owner->getBasePrice();
        
        if ($owner->PricingGroupID) {
            $group = $owner->Pricinggroup();
            $qty = 0;
            foreach ($estimate->Items() as $item) {
                if ($item->isInPricingGroup($group)) {
                    if ($item->ID == $id) {
                        $qty += $new_qty;
                    } else {
                        $qty += $item->Quantity;
                    }
                }
            }
            $brackets = $group->Brackets()->sort('Quantity', 'ASC');
            foreach ($brackets as $bracket) {
                if ($bracket->Quantity <= $qty) {
                    $price = $price - $bracket->Price;
                }
            }
        } elseif ($brackets = $this->owner->PricingBrackets()->sort('Quantity', 'ASC')) {
            $qty = 0;
            foreach ($estimate->Items() as $item) {
                if ($item->FindStockItem() == $owner) {
                    $qty = $item->Quantity;
                }
            }
            foreach ($brackets as $bracket) {
                if ($bracket->Quantity <= $qty) {
                    $price = $bracket->Price;
                }
            }
        }

        return $price;
    }

    public function getPricingTable()
    {
        return $this->owner->renderWith(
            'ilateral\\SilverStripe\\BulkPricing\\Includes\\PricingTable'
        );
    }
}