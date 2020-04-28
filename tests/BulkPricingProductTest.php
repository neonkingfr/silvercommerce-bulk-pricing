<?php

namespace SilverCommerce\BulkPricing\Tests;

use SilverCommerce\CatalogueAdmin\Model\CatalogueProduct;
use SilverStripe\Dev\SapphireTest;

class BulkPricingProductTest extends SapphireTest
{
    protected static $fixture_file = 'BulkPricing.yml';

    public function testGetValidPricingBrackets()
    {
        $pr_items = $this->objFromFixture(CatalogueProduct::class, 'product_one');
        $pr_group = $this->objFromFixture(CatalogueProduct::class, 'product_two');
        $pr_cat_one = $this->objFromFixture(CatalogueProduct::class, 'product_three');
        $pr_cat_two = $this->objFromFixture(CatalogueProduct::class, 'product_four');

        $this->assertCount(2, $pr_items->getValidPricingBrackets());
        $this->assertEquals(4, $pr_items->getValidPricingBrackets()->first()->Price);

        $this->assertCount(2, $pr_group->getValidPricingBrackets());
        $this->assertEquals(1, $pr_group->getValidPricingBrackets()->first()->Price);

        $this->assertCount(2, $pr_cat_one->getValidPricingBrackets());
        $this->assertEquals(4, $pr_cat_one->getValidPricingBrackets()->first()->Price);

        $this->assertCount(2, $pr_cat_two->getValidPricingBrackets());
        $this->assertEquals(4, $pr_cat_two->getValidPricingBrackets()->first()->Price);

        $this->assertCount(0, $pr_items->getValidPricingBrackets(1));
        $this->assertCount(1, $pr_items->getValidPricingBrackets(6));
        $this->assertCount(1, $pr_items->getValidPricingBrackets(10));

        $this->assertCount(0, $pr_group->getValidPricingBrackets(1));
        $this->assertCount(1, $pr_group->getValidPricingBrackets(6));
        $this->assertCount(1, $pr_group->getValidPricingBrackets(10));

        $this->assertCount(0, $pr_cat_one->getValidPricingBrackets(1));
        $this->assertCount(1, $pr_cat_one->getValidPricingBrackets(5));
        $this->assertCount(1, $pr_cat_one->getValidPricingBrackets(6));
        $this->assertCount(1, $pr_cat_one->getValidPricingBrackets(10));

        $this->assertCount(0, $pr_cat_two->getValidPricingBrackets(1));
        $this->assertCount(1, $pr_cat_two->getValidPricingBrackets(6));
        $this->assertCount(1, $pr_cat_two->getValidPricingBrackets(10));
    }

    public function testGetBulkPrice()
    {
        $pr_items = $this->objFromFixture(CatalogueProduct::class, 'product_one');
        $pr_group = $this->objFromFixture(CatalogueProduct::class, 'product_two');
        $pr_cat_one = $this->objFromFixture(CatalogueProduct::class, 'product_three');
        $pr_cat_two = $this->objFromFixture(CatalogueProduct::class, 'product_four');

        $this->assertEquals(5, $pr_items->getBulkPrice(1));
        $this->assertEquals(4, $pr_items->getBulkPrice(6));
        $this->assertEquals(4, $pr_items->getBulkPrice(9));
        $this->assertEquals(3, $pr_items->getBulkPrice(10));
        $this->assertEquals(3, $pr_items->getBulkPrice(14));
        $this->assertEquals(5, $pr_items->getBulkPrice(15));

        $this->assertEquals(10, $pr_group->getBulkPrice(1));
        $this->assertEquals(9, $pr_group->getBulkPrice(5));
        $this->assertEquals(9, $pr_group->getBulkPrice(6));
        $this->assertEquals(9, $pr_group->getBulkPrice(9));
        $this->assertEquals(8, $pr_group->getBulkPrice(10));
        $this->assertEquals(8, $pr_group->getBulkPrice(12));
        $this->assertEquals(8, $pr_group->getBulkPrice(14));
        $this->assertEquals(10, $pr_group->getBulkPrice(15));

        $this->assertEquals(15, $pr_cat_one->getBulkPrice(1));
        $this->assertEquals(11, $pr_cat_one->getBulkPrice(5));
        $this->assertEquals(11, $pr_cat_one->getBulkPrice(6));
        $this->assertEquals(11, $pr_cat_one->getBulkPrice(9));
        $this->assertEquals(9, $pr_cat_one->getBulkPrice(10));
        $this->assertEquals(9, $pr_cat_one->getBulkPrice(12));
        $this->assertEquals(9, $pr_cat_one->getBulkPrice(14));
        $this->assertEquals(15, $pr_cat_one->getBulkPrice(15));

        $this->assertEquals(20, $pr_cat_two->getBulkPrice(1));
        $this->assertEquals(16, $pr_cat_two->getBulkPrice(5));
        $this->assertEquals(16, $pr_cat_two->getBulkPrice(6));
        $this->assertEquals(16, $pr_cat_two->getBulkPrice(9));
        $this->assertEquals(14, $pr_cat_two->getBulkPrice(10));
        $this->assertEquals(14, $pr_cat_two->getBulkPrice(12));
        $this->assertEquals(14, $pr_cat_two->getBulkPrice(14));
        $this->assertEquals(20, $pr_cat_two->getBulkPrice(15));
    }
}
