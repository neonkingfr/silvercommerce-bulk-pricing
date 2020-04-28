<?php

namespace SilverCommerce\BulkPricing\Tests;

use SilverCommerce\OrdersAdmin\Model\LineItem;
use SilverStripe\Dev\SapphireTest;

class BulkPricingLineItemTest extends SapphireTest
{
    protected static $fixture_file = 'BulkPricing.yml';

    public function testOnBeforeWriteStandard()
    {
        $item = $this->objFromFixture(LineItem::class, 'line_item_one');

        $this->assertEquals(5, $item->getBasePrice());

        $item->Quantity = 4;
        $item->write();
        $this->assertEquals(5, $item->getBasePrice());

        $item->Quantity = 5;
        $item->write();
        $this->assertEquals(4, $item->getBasePrice());

        $item->Quantity = 6;
        $item->write();
        $this->assertEquals(4, $item->getBasePrice());

        $item->Quantity = 9;
        $item->write();
        $this->assertEquals(4, $item->getBasePrice());

        $item->Quantity = 10;
        $item->write();
        $this->assertEquals(3, $item->getBasePrice());

        $item->Quantity = 12;
        $item->write();
        $this->assertEquals(3, $item->getBasePrice());

        $item->Quantity = 14;
        $item->write();
        $this->assertEquals(3, $item->getBasePrice());

        $item->Quantity = 1;
        $item->write();
        $this->assertEquals(5, $item->getBasePrice());
    }

    public function testOnBeforeWriteGroup()
    {
        $item = $this->objFromFixture(LineItem::class, 'line_item_two');

        $this->assertEquals(10, $item->getBasePrice());

        $item->Quantity = 4;
        $item->write();
        $this->assertEquals(10, $item->getBasePrice());

        $item->Quantity = 5;
        $item->write();
        $this->assertEquals(9, $item->getBasePrice());

        $item->Quantity = 6;
        $item->write();
        $this->assertEquals(9, $item->getBasePrice());

        $item->Quantity = 9;
        $item->write();
        $this->assertEquals(9, $item->getBasePrice());

        $item->Quantity = 10;
        $item->write();
        $this->assertEquals(8, $item->getBasePrice());

        $item->Quantity = 12;
        $item->write();
        $this->assertEquals(8, $item->getBasePrice());

        $item->Quantity = 14;
        $item->write();
        $this->assertEquals(8, $item->getBasePrice());

        $item->Quantity = 1;
        $item->write();
        $this->assertEquals(10, $item->getBasePrice());
    }

    public function testOnBeforeWriteCats()
    {
        $item = $this->objFromFixture(LineItem::class, 'line_item_three');

        $this->assertEquals(15, $item->getBasePrice());

        $item->Quantity = 4;
        $item->write();
        $this->assertEquals(15, $item->getBasePrice());

        $item->Quantity = 5;
        $item->write();
        $this->assertEquals(11, $item->getBasePrice());

        $item->Quantity = 6;
        $item->write();
        $this->assertEquals(11, $item->getBasePrice());

        $item->Quantity = 9;
        $item->write();
        $this->assertEquals(11, $item->getBasePrice());

        $item->Quantity = 10;
        $item->write();
        $this->assertEquals(9, $item->getBasePrice());

        $item->Quantity = 12;
        $item->write();
        $this->assertEquals(9, $item->getBasePrice());

        $item->Quantity = 14;
        $item->write();
        $this->assertEquals(9, $item->getBasePrice());

        $item->Quantity = 1;
        $item->write();
        $this->assertEquals(15, $item->getBasePrice());

        $item = $this->objFromFixture(LineItem::class, 'line_item_four');

        $this->assertEquals(20, $item->getBasePrice());

        $item->Quantity = 4;
        $item->write();
        $this->assertEquals(20, $item->getBasePrice());

        $item->Quantity = 5;
        $item->write();
        $this->assertEquals(16, $item->getBasePrice());

        $item->Quantity = 6;
        $item->write();
        $this->assertEquals(16, $item->getBasePrice());

        $item->Quantity = 9;
        $item->write();
        $this->assertEquals(16, $item->getBasePrice());

        $item->Quantity = 10;
        $item->write();
        $this->assertEquals(14, $item->getBasePrice());

        $item->Quantity = 12;
        $item->write();
        $this->assertEquals(14, $item->getBasePrice());

        $item->Quantity = 14;
        $item->write();
        $this->assertEquals(14, $item->getBasePrice());

        $item->Quantity = 1;
        $item->write();
        $this->assertEquals(20, $item->getBasePrice());
    }
}
