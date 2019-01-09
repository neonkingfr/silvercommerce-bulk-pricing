<% if $PricingBrackets %>
    <h3>Bulk Pricing</h3>
    <table class="table table-sm">
        <tr>
            <th>Quantity</th>
            <th>Price per Unit</th>
        </tr>
        <tr>
            <td>1+</td>
            <td>
                <% if $IncludesTax %>
                    {$PriceAndTax.nice}
                <% else %>
                    {$Price.nice}
                <% end_if %>
            </td>
        </tr>
        <% loop $PricingBrackets %>
            <tr>
                <td>{$Quantity}+</td>
                <td>
                    <% if $Product.IncludesTax %>
                        {$PriceAndTax.nice}
                    <% else %>
                        {$Price.Nice}
                    <% end_if %>
                </td>
            </tr>
        <% end_loop %>
        <% if TaxString %>
            <tr>
                <td>&nbsp;</td>
                <td>
                    <small class="tax"> 
                        &nbsp;{$TaxString}
                    </small>
                </td>
            </tr>
        <% end_if %>
    </table>
<% end_if %>
