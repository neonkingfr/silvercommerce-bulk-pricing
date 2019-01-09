# SilverCommerce Bulk Pricing Setup

Adds pricing to products based on the quantity in the order. Allows adding price brackets to products which alter the price when a minimum quantity is ordered.

Each price bracket has a minimum quantity field and a new price field. When the item is added to an order or modified, it will find the bracket with the highest minimum that is below the current quantity and assign that price to the item in the order.

## Installation

Install this via composer:

    composer require silvercommerce/bulk-pricing

Not using composer? [Install composer](https://getcomposer.org/)

Now run `dev/build` (either via the browser, or using sake).

## Configuration

A pricing table can be added in the templates via the '$PricingTable' variable. 