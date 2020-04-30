# SilverCommerce Bulk Pricing Setup

Adds custom pricing to products based on the quantity of products added to an order.

*NOTE* v2 of this module heavily re-writes v1. There is a migration script that migrates data from v1,
but it is advised that you double check your pricing brackets before upgrading a live site. 

## Installation

Install this via composer:

    composer require silvercommerce/bulk-pricing

Not using composer? [Install composer](https://getcomposer.org/)

Now run `dev/build` (either via the browser, or using sake).

## Usage

Custom pricing can be done in several different ways

### Product Based

The simplest way to use this mopdule is to add pricing brackets to a product in the catalogue.

You must specify The minimum and maximum number of products for each bracket and a new price.

If you use the "Reduce Product Price" option, the product's price will be reduced by the specified
amount.

### Group Based

If you have a lot of products with the same price adjustments, you can create a "group" vis `SiteConfig`.
The `BulkPricingGroup` can then be linked to products and all linked products will follow the pricing
brackets.

You can also link a `BulkPricingGroup` to a Product Category. If you do this, all products in
the slected categoy (AND its children) will have the pricing rules applied.

#### Pricing Bands for ALL products in a group

If you want all products in a group to apply for custom pricing (EG: you have 3 of Product X and 2 of Product Y),
You have to tick the 'Apply Pricing To Any Combination Of Products In Group' checkbox, otherwise bulk pricing
will only be applied to individual products in the group. 

NOTE: It is important to keep track of `BulkPricingGroup`'s applied to Products and Categories. You can
get odd results if you accidentaly link multiple groups to the same product.

## Rendering A Pricing Table On Your Product

This module also includes a pricing table can be added to a product template. To render this you can add
the following variable to your templates

    $PricingTable 
