<div id="app-settings">
	<div id="app-settings-header">
		<button class="settings-button"
				data-apps-slide-toggle="#app-settings-content"
		>Settings</button>
	</div>
	<div id="app-settings-content">
		<label for="api_key">API Key</label>
		<a href="https://github.com/j4ym0/nextcloud-tv-show-namer/tree/0.0.1#getting-your-api-key" target="_blank" class="help">Get your api key</a>
		<input type="input" id="tmdb_api_key" class="input w-100" value="<?php p($_['tmdb_api_key']);?>">
	</div>
</div>
