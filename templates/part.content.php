<script id="content-tpl" type="text/x-handlebars-template">
	{{#if account}}
		<h2>{{ account.account_name }} - {{ account.bank_name }}</h2>
		<table id="transactions_table" class="display" cellspacing="0" width="95%">
			<thead>
				<tr>
					<th title="Field #1">id</th>
					<th title="Field #2">date</th>
					<th title="Field #3">amount</th>
					<th title="Field #4">account</th>
					<th title="Field #5">dst_account</th>
					<th title="Field #6">paymode</th>
					<th title="Field #7">flags</th>
					<th title="Field #8">info</th>
					<th title="Field #9">user_id</th>
				</tr>
			</thead>
		</table>
	{{else}}
		<div class="emptycontent">
			<div class="icon-folder"></div>
			<?php p($l->t('Nothing here. Take your first transactions.')); ?>
		</div>
	{{/if}}
</script>
<div id="transactions"></div>