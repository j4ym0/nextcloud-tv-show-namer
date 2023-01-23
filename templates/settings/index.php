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
			<label for="preferred_language"><?php p($l->t('Preferred Naming Language')); ?></label>
			<select id="preferred_language" data-selected-value="<?php p($_['preferred_language']);?>" class="w-100">
				<option value="en">English</option>
				<option value="es">Español (España)</option>
				<option value="fr">Français</option>
				<option value="de">Deutsch (Persönlich: Du)</option>
				<option value="de-DE">Deutsch (Förmlich: Sie)</option>
				<option value="ja">Japanese (日本語)</option>
				<option value="ar">اللغة العربية</option>
				<option value="ru">Русский</option>
				<option value="nl">Nederlands</option>
				<option value="it">Italiano</option>
				<option value="pt-BR">Português Brasileiro</option>
				<option value="pt-PT">Português</option>
				<option value="da">Dansk</option>
				<option value="sv">Svenska</option>
				<option value="tr">Türkçe</option>
				<option value="zh-CN">简体中文</option>
				<option value="ko">한국어</option>
				<option disabled="disabled">──────────</option>
				<option value="id">Bahasa Indonesia</option>
				<option value="br">Brezhoneg</option>
				<option value="ca">Català</option>
				<option value="et-EE">Eesti</option>
				<option value="en-GB">English (British English)</option>
				<option value="es-AR">Español (Argentina)</option>
				<option value="es-CL">Español (Chile)</option>
				<option value="es-CO">Español (Colombia)</option>
				<option value="es-CR">Español (Costa Rica)</option>
				<option value="es-DO">Español (Dominican Republic)</option>
				<option value="es-EC">Español (Ecuador)</option>
				<option value="es-SV">Español (El Salvador)</option>
				<option value="es-GT">Español (Guatemala)</option>
				<option value="es-HN">Español (Honduras)</option>
				<option value="es-MX">Español (México)</option>
				<option value="es-NI">Español (Nicaragua)</option>
				<option value="es-PA">Español (Panama)</option>
				<option value="es-PY">Español (Paraguay)</option>
				<option value="es-PE">Español (Peru)</option>
				<option value="es-PR">Español (Puerto Rico)</option>
				<option value="es-UY">Español (Uruguay)</option>
				<option value="eo">Esperanto</option>
				<option value="eu">Euskara</option>
				<option value="gl">Galego</option>
				<option value="hr">Hrvatski</option>
				<option value="lv">Latviešu</option>
				<option value="lt-LT">Lietuvių</option>
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
				<option value="ka-GE">ქართული</option>
				<option value="zh-TW">正體中文（臺灣）</option>
				<option value="zh-HK">正體中文（香港）</option>
			</select>
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
