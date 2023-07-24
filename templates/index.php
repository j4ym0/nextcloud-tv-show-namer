<?php
script('tvshownamer', array('jquery-3.7.0.min', 'script');
style('tvshownamer', 'style');
?>

<div id="app-navigation">
	<?php #print_unescaped($this->inc('navigation/index')); ?>
	<?php print_unescaped($this->inc('settings/index')); ?>
</div>
<div id="app-content">
	<?php print_unescaped($this->inc('content/index')); ?>
</div>
