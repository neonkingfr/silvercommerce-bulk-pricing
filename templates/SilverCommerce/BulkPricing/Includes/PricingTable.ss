<% if $ValidPricingBrackets %>
    <table class="table table-sm">
        <tr>
            <th><%t SilverCommerce\BulkPricing\Model\BulkPricingBracket.Quantity "Quantity" %></th>
            <th><%t SilverCommerce\BulkPricing\Model\BulkPricingBracket.PricePerUnit "Price per Unit" %></th>
        </tr>
        <% loop $ValidPricingBrackets %>
            <tr>
                <td>{$MinQTY} - {$MaxQTY}</td>
                <td>
                    $NicePrice
                </td>
            </tr>
        <% end_loop %>
    </table>
<% end_if %>
