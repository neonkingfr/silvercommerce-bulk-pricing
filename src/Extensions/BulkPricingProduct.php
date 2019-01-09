<?php

namespace SilverCommerce\BulkPricing\Extensions;

use SilverStripe\ORM\ArrayList;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\FieldType\DBCurrency;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverCommerce\BulkPricing\Model\BulkPricingBracket;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverCommerce\CatalogueAdmin\Model\CatalogueProduct;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;

class BulkPricingProduct extends DataExtension
{
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

        $fields->addFieldToTab(
            'Root.BulkPricing',
            GridField::create(
                'PricingBrackets',
                'Bulk Prices',
                $this->owner->PricingBrackets()
            )->setConfig($config)
        );
    }

    /**
     * fetch the correct base price based on the provided quantity
     *
     * @param int $qty
     * @return DBCurrency
     */
    public function getBulkPrice($qty)
    {
        $price = $this->owner->BasePrice;

        if ($brackets = $this->owner->PricingBrackets()) {
            foreach ($brackets as $bracket) {
                if ($bracket->Quantity < $qty) {
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