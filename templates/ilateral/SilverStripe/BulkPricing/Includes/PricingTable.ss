<% if $PricingBrackets %>
    <h3>Bulk Pricing</h3>
    <table class="table table-sm">
        <tr>
            <th>Quantity</th>
            <th>Price per Unit</th>
        </tr>
        <tr>
            <td>1+</td>
            <td>$Price.Nice</td>
        </tr>
        <% loop $PricingBrackets %>
            <tr>
                <td>{$Quantity}+</td>
                <td>$Price.Nice</td>
            </tr>
        <% end_loop %>
    </table>
<% end_if %>
