<script id="content-tpl" type="text/x-handlebars-template">
	{{#if account}}
		<h2>{{ account.account_name }} - {{ account.bank_name }}</h2><h2 id="total-balance"></h2>
		<table id="transactions_table" class="display" cellspacing="0" width="95%">
		</table>
	{{else}}
		<div class="emptycontent">
			<div class="icon-folder"></div>
			<?php p($l->t('Nothing here. Take your first transactions.')); ?>
		</div>
	{{/if}}
</script>
<div id="transactions"></div>