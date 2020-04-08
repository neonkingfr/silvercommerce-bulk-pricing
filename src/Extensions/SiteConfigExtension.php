<?php

namespace SilverCommerce\BulkPricing\Extensions;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverCommerce\BulkPricing\Model\BulkPricingGroup;
use SilverStripe\Forms\GridField\GridFieldConfig_RecordEditor;

class BulkSiteConfigExtension extends DataExtension
{
    public function updateCMSFields(\SilverStripe\Forms\FieldList $fields)
    {
        $config = GridFieldConfig_RecordEditor::create();
        
        // Add config sets
        $fields->addFieldToTab(
            'Root.Shop',
            ToggleCompositeField::create(
                'BulkPricing',
                _t("BulkPricing.BulkPricing", "Bulk Pricing"),
                [
                    LiteralField::create("BPPadding", "<br/>"),
                    GridField::create(
                        'BulkPricingGroups',
                        '',
                        BulkPricingGroup::get()
                    )->setConfig($config)
                ]
            )
        );
    }
}