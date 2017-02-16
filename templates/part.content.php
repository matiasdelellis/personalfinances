<script id="content-tpl" type="text/x-handlebars-template">
	{{#if account}}
		<div class="emptycontent">
			<div class="icon-folder"></div>
			{{ account.name }}
			{{ account.type }}
			{{ account.initial }}
		</div>
	{{else}}
		<div class="emptycontent">
			<div class="icon-folder"></div>
			<?php p($l->t('Nothing here. Take your first transactions.')); ?>
		</div>
	{{/if}}
</script>
<div id="transactions"></div>