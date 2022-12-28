<div id="app-settings">
	<div id="app-settings-header">
		<button class="settings-button" data-apps-slide-toggle="#app-settings-content"><?php p($l->t('Settings')); ?></button>
	</div>
	<div id="app-settings-content">
		<div>
			<label for="api_key"><?php p($l->t('API Key')); ?></label>
			<a href="https://github.com/j4ym0/nextcloud-tv-show-namer/tree/main#getting-your-api-key" target="_blank" class="help"><?php p($l->t('Get your API key here')); ?></a>
			<input type="input" id="tmdb_api_key" class="input w-100" value="<?php p($_['tmdb_api_key']);?>">
		</div>
		<div>
			<label for="file_name_structure"><?php p($l->t('Naming Structure')); ?></label>
			<a href="https://github.com/j4ym0/nextcloud-tv-show-namer/tree/main#naming-guide" target="_blank" class="help"><?php p($l->t('Examples')); ?></a>
			<input type="input" id="file_name_structure" class="input w-100" value="<?php p($_['file_name_structure']);?>">
		</div>
		<div class="mar-top-20">
			<lable for="hide_matching"><?php p($l->t('Hide matching entries')); ?></lable>
			<label class="toggle a-right">
				<input type="checkbox" id="hide_matching" <?php p($_['hide_matching']);?>>
				<span class="slider"></span>
			</label>
		</div>
	</div>
</div>
