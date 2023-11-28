<div id="app-settings">
	<div id="app-settings-header">
		<button type="button" class="settings-button" id="app-datasource-button" data-apps-slide-toggle="#app-datasource-content"><?php p($l->t('Data Source')); ?></button>
	</div>
	<div id="app-datasource-content">
		<div class="tvdb">
			<div class="powered"><span>TheTVDB</span><a href="https://thetvdb.com/subscribe" target="_blank" class="help"><img src="<?php echo \OC::$server->getURLGenerator()->imagePath('tvshownamer', 'tvdb_dark.png') ?>" height="20px" title="<?php p($l->t('Metadata provided by TheTVDB. Please consider adding missing information or subscribing.')); ?>" /></a></div>
			<div  class="label-group">
				<label for="enable_tvdb" title="<?php p($l->t('Enable')); ?> TheTVDB <?php p($l->t('data source')); ?>"><?php p($l->t('Search')); ?> TheTVDB</label>
				<label class="toggle a-right">
					<input type="checkbox" name="enable_tvdb" id="enable_tvdb" class="setting_toggle" data-setting="enable_tvdb" <?php p($_['enable_tvdb']);?>>
					<span class="slider"></span>
				</label>
			</div>
		</div>
		<div class="spacer"></div>
		<div class="tmdb">
			<div class="powered"><span>The Movie DB</span><a href="https://www.themoviedb.org/" target="_blank" class="help"><img src="<?php echo \OC::$server->getURLGenerator()->imagePath('tvshownamer', 'tmdb.svg') ?>" height="20px" title="TV Show Namer <?php p($l->t('uses TMDB and the TMDB APIs but is not endorsed, certified, or otherwise approved by TMDB.')); ?>" /></a></div>
			<div class="label-group">
				<label for="enable_tmdb" title="<?php p($l->t('Enable')); ?> The Movie DB <?php p($l->t('data source')); ?>"><?php p($l->t('Search')); ?> TMDB</label>
				<label class="toggle a-right">
					<input type="checkbox" name="enable_tmdb" id="enable_tmdb" class="setting_toggle" data-setting="enable_tmdb" <?php p($_['enable_tmdb']);?>>
					<span class="slider"></span>
				</label>
			</div>
			<div class="label-group">
				<label for="tmdb_api_key" title="<?php p($l->t('Search')); ?> The Movie DB <?php p($l->t('using your own personal API Key (Not required)')); ?>"><?php p($l->t('Personal API Key')); ?></label>
				<a href="https://github.com/j4ym0/nextcloud-tv-show-namer/tree/main#getting-your-api-key" target="_blank" class="help"><?php p($l->t('Personal API key help')); ?></a>
				<input type="input" id="tmdb_api_key" class="input w-100" placeholder="<?php p($l->t('Personal API key')); ?>" value="<?php p($_['tmdb_api_key']);?>">
			</div>
		</div>
	</div>
	<div id="app-settings-header">
		<button type="button" class="settings-button" id="app-settings-button" data-apps-slide-toggle="#app-settings-content"><?php p($l->t('Settings')); ?></button>
	</div>
	<div id="app-settings-content">
		<div>
			<label for="preferred_language"><?php p($l->t('Preferred Naming Language')); ?></label>
			<select id="preferred_language" data-selected-value="<?php p($_['preferred_language']);?>" class="w-100">
				<option value="en">English</option>
				<option value="es">Español</option>
				<option value="fr">Français</option>
				<option value="de">Deutsch</option>
				<option value="ja">Japanese (日本語)</option>
				<option value="ar">اللغة العربية</option>
				<option value="ru">Русский</option>
				<option value="nl">Nederlands</option>
				<option value="it">Italiano</option>
				<option value="pt">Português</option>
				<option value="da">Dansk</option>
				<option value="sv">Svenska</option>
				<option value="tr">Türkçe</option>
				<option value="zh">简体中文</option>
				<option value="ko">한국어</option>
				<option disabled="disabled">──────────</option>
				<option value="id">Bahasa Indonesia</option>
				<option value="br">Brezhoneg</option>
				<option value="ca">Català</option>
				<option value="et">Eesti</option>
				<option value="eo">Esperanto</option>
				<option value="eu">Euskara</option>
				<option value="gl">Galego</option>
				<option value="hr">Hrvatski</option>
				<option value="lv">Latviešu</option>
				<option value="lt">Lietuvių</option>
				<option value="hu">Magyar</option>
				<option value="nb">Norsk bokmål</option>
				<option value="oc">Occitan</option>
				<option value="pl">Polski</option>
				<option value="ro">Română</option>
				<option value="sk">Slovenčina</option>
				<option value="sl">Slovenščina</option>
				<option value="vi">Tiếng Việt</option>
				<option value="sc">sardu</option>
				<option value="fi">suomi</option>
				<option value="is">Íslenska</option>
				<option value="cs">Čeština</option>
				<option value="el">Ελληνικά</option>
				<option value="bg">Български</option>
				<option value="mk">Македонски</option>
				<option value="sr">Српски</option>
				<option value="uk">Українська</option>
				<option value="he">עברית</option>
				<option value="fa">فارسى</option>
				<option value="th">ภาษาไทย - Thai</option>
				<option value="lo">ຂີ້ຕົວະ</option>
			</select>
		</div>
		<div>
			<label for="file_name_structure"><?php p($l->t('Naming Structure')); ?></label>
			<a href="https://github.com/j4ym0/nextcloud-tv-show-namer/tree/main#naming-guide" target="_blank" class="help"><?php p($l->t('Examples')); ?></a>
			<input type="input" id="file_name_structure" class="input w-100" value="<?php p($_['file_name_structure']);?>">
		</div>
		<div class="mar-top-20">
			<label for="hide_matching"><?php p($l->t('Hide matching entries')); ?></label>
			<label class="toggle a-right">
				<input type="checkbox" name="hide_matching" id="hide_matching" class="setting_toggle" data-setting="hide_matching" <?php p($_['hide_matching']);?>>
				<span class="slider"></span>
			</label>
		</div>
	</div>
</div>
