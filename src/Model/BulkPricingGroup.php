<?php

namespace SilverCommerce\BulkPricing\Model;

use SilverStripe\ORM\DataObject;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverCommerce\BulkPricing\Model\BulkPricingBracket;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverCommerce\CatalogueAdmin\Model\CatalogueProduct;
use SilverCommerce\CatalogueAdmin\Model\CatalogueCategory;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use SilverCommerce\BulkPricing\Model\GroupBulkPricingBracket;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;

/**
 * @method CatalogueCategory Category
 * @method \SilverStripe\ORM\HasManyList PricingBrackets
 */
class BulkPricingGroup extends DataObject
{
    private static $table_name = 'BulkPricingGroup';

    private static $db = [
        'Title' => 'Varchar',
        'GroupedPricing' => 'Boolean'
    ];
    
    private static $has_one = [
        'Category' => CatalogueCategory::class,
    ];

    private static $has_many = [
        'Products' => CatalogueProduct::class,
        'PricingBrackets' => BulkPricingBracket::class,
        'Brackets' => GroupBulkPricingBracket::class // Legacy, soon to be removed
    ];

    private static $field_labels = [
        'GroupedPricing' => 'Apply Pricing To Any Combination Of Products In Group?'
    ];

    /**
     * Compile full list of products in this group
     *
     * @return \SilverStripe\ORM\SS_List
     */
    public function getValidProducts()
    {
        $ids = array_merge(
            $this->Products()->column("ID"),
            $this->Category()->AllProducts()->column('ID')
        );

        $products = CatalogueProduct::get()->filter("ID", $ids);

        return $products;
    }

    public function getCMSFields()
    {
        $self = $this;

        $this->beforeUpdateCMSFields(
            function ($fields) use ($self) {
                $fields->removeByName('Brackets');
                $pricing_field = $fields->dataFieldByName('PricingBrackets');

                if (!empty($pricing_field)) {
                    $config = GridFieldConfig::create()
                        ->addComponent(new GridFieldButtonRow('before'))
                        ->addComponent(new GridFieldToolbarHeader())
                        ->addComponent(new GridFieldTitleHeader())
                        ->addComponent(new GridFieldEditableColumns())
                        ->addComponent(new GridFieldDeleteAction())
                        ->addComponent(new GridFieldAddNewInlineButton());
                    $pricing_field->setConfig($config);
                }
            }
        );

        return parent::getCMSFields();
    }
}
