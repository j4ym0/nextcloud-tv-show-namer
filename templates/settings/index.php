<div id="app-settings">
	<div id="app-settings-header">
		<button class="settings-button"
				data-apps-slide-toggle="#app-settings-content"
		>Settings</button>
	</div>
	<div id="app-settings-content">
		<label for="api_key">API Key</label>
		<input type="input" id="tmdb_api_key" class="input w-100" value="<?php p($_['tmdb_api_key']);?>">
	</div>
</div>
