<div id="loading-can" style="display: none;">
	<div class="mask icon-loading"></div>
  <p><?php p($l->t('Please wait')); ?>...</p>
</div>

<div id="headding">
 <a id="scanfolder" class="button"><span><?php p($l->t('Scan Folder')); ?></span></a>
 <span class="current_folder"><?php p($l->t('Choose a folder to start')); ?></span>
</div>
<div id="display-can"><?php echo $_['info_message']; ?></div>
