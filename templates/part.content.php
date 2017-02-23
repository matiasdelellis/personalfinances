<script id="content-tpl" type="text/x-handlebars-template">
	{{#if account}}
		<h2>{{ account.account_name }} - {{ account.bank_name }}</h2><h2 id="total-balance"></h2>
		<table id="transactions_table" class="display" cellspacing="0" width="95%">
		</table>
	{{else}}
		<h2>Last 30 day report</h2>
		<canvas id="reportChart" width="150" height="50"></canvas>
		<table id="report_table" class="display" cellspacing="0" width="95%">
	{{/if}}
</script>
<div id="transactions"></div>