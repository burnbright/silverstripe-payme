<h1>$Title</h1>
$Content
<% if Invoice %>
	<h2>Invoice</h2>
	<% with Invoice %>
		<% with Payment %>
			<h3>Payment</h3>
			<table>
				<tbody>
					<tr>
						<td>Gateway</td>
						<td>$Gateway</td>
					</tr>
					<tr>
						<td>Money</td>
						<td>$Money.Nice</td>
					</tr>
					<tr>
						<td>Status</td>
						<td>$Status</td>
					</tr>
				</tbody>
			</table>

			<% if Transactions %>
				<h3>Transactions:</h3>
				<table>
					<thead>
						<tr>
							<td>Type</td>
							<td>Identifier</td>
							<td>Reference</td>
							<td>Message</td>
							<td>Code</td>
						</tr>
					</thead>
					<% loop Transactions %>
						<tr>
							<td>$Type</td>
							<td>$Identifier</td>
							<td>$Reference</td>
							<td>$Message</td>
							<td>$Code</td>
						</tr>
					<% end_loop %>
				</table>
			<% end_if %>
			
		<% end_with %>

	<% end_with %>
<% end_if %>
$Form
$PageComments