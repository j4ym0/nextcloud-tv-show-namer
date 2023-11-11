<div id="loading-can" style="display: none;">
	<div class="mask icon-loading"></div>
  <p><?php p($l->t('Please wait')); ?>...</p>
</div>

<div id="heading">
  <a id="scanfolder" class="button"><span><?php p($l->t('Scan Folder')); ?></span></a>
  <span class="current_folder"><?php p($l->t('Choose a folder to start')); ?></span>
  <ul class="source_selector">
    <li class="source_button <?php echo $_['tmdb_active']; ?><?php echo $_['enable_tmdb'] != "checked" ? ' hide' : ''; ?>" id="source_tmdb" title="<?php p($l->t('Change data source to')); ?> TMDB" data-setting="active_datasource" data-source="tmdb"><button type="button"></button></li>
    <li class="source_button <?php echo $_['tvdb_active']; ?><?php echo $_['enable_tvdb'] != "checked" ? ' hide' : ''; ?>" id="source_tvdb" title="<?php p($l->t('Change data source to')); ?> TVDB" data-setting="active_datasource" data-source="tvdb"><button type="button"></button></li>
  </ul>
</div>
<div id="display-can"><?php echo $_['info_message']; ?></div>
