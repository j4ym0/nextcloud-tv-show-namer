<div id="app-settings">
	<div id="app-settings-header">
		<button class="settings-button"
				data-apps-slide-toggle="#app-settings-content"
		>Settings</button>
	</div>
	<div id="app-settings-content">
		<div>
			<label for="api_key">API Key</label>
			<a href="https://github.com/j4ym0/nextcloud-tv-show-namer/tree/main#getting-your-api-key" target="_blank" class="help">Get your api key</a>
			<input type="input" id="tmdb_api_key" class="input w-100" value="<?php p($_['tmdb_api_key']);?>">
		</div>
		<div>
			<label for="file_name_structure">Naming Structure</label>
			<a href="https://github.com/j4ym0/nextcloud-tv-show-namer/tree/main#naming-guide" target="_blank" class="help">Examples</a>
			<input type="input" id="file_name_structure" class="input w-100" value="<?php p($_['file_name_structure']);?>">
		</div>
	</div>
</div>
