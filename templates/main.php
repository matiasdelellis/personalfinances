<?php
vendor_script('personalfinances', 'handlebars');
vendor_script('personalfinances', 'jquery.dataTables');
vendor_script('personalfinances', 'Chart');
vendor_style('personalfinances', 'jquery.dataTables');
script('personalfinances', 'script');
style('personalfinances', 'style');
?>

<div id="app">
	<div id="app-navigation">
		<?php print_unescaped($this->inc('part.navigation')); ?>
		<?php print_unescaped($this->inc('part.settings')); ?>
	</div>

	<div id="app-content">
		<div id="app-content-wrapper">
			<?php print_unescaped($this->inc('part.content')); ?>
		</div>
	</div>
</div>
