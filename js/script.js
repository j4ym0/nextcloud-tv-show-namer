(function () {
  var baseUrl = OC.generateUrl('/apps/tvshownamer');
  var can = document.getElementById('display-can');
  var loader = document.getElementById('loading-can');
  var header = document.getElementById('headding');
  var current_posts = 0;
  var datasource = '';
  var checkElForCallback = function(el, callback) {
    if ($(el).length) {
      $(el).each(function() {$(this).click(function() {callback($(this))})});
//    dont need this at the mo
//    } else {
//      setTimeout(function() { checkElForCallback(el, callback);}, 100);
    }
  };


  function render(data) {
    var hide_matching = $('#hide_matching').prop('checked');
    if (data.success){
      if (data.action === "update"){
        $('#'+data.element).replaceWith(build_file_list(data.file[0]));
      }else{
        var r = '';
        r += build_show_header(data);
        r += '<table class="file_list">';
        r += build_file_list_header();
        for(var i in data.files) {
          r += build_file_list(data.files[i],hide_matching);
        }
        r+='</table>';
        can.innerHTML=r;
        if (data.show_info != null){
          if (typeof data.show_info.id != "undefined"){
            set_active_datasource(data.show_info.source);
        }}
        checkElForCallback('button#confirm', function(t){rename_file(t);});
        checkElForCallback('input.select-file', function(t){select_file();});
        checkElForCallback('input.select-all', function(t){select_all();});
        checkElForCallback('button#next_title', function(t){next_title(t);});
        checkElForCallback('button#update-all', function(){submit_selected();});
        $('.current_folder').html('<a href="../files/?dir=' + data.path + '" title="'+t('tvshownamer', 'Open {path} in Nextcloud', {path: data.path})+'" alt="'+t('tvshownamer', 'Click to open {path} in Nextcloud', {path: data.path})+'">' + data.path + '</a><a data-path="'+data.path +'" id="rescan" class="reload" title="'+t('tvshownamer', 'Rescan folder')+'" alt="'+t('tvshownamer', 'Rescan selected folder')+'"></a>');
        checkElForCallback('a.reload', function(t){  get_data('scan', {'scan_folder' : $(t).data('path'), 'datasource': datasource}, render);});
      }
    }else{
      message(data);
    }
  }
  function message(json = null){
    OC.Notification.showTemporary(json.message != null ? json.message : t('tvshownamer', 'Unexpected error'));
  }
  function simple_message(msg){
    OC.Notification.showTemporary(msg);
  }
  function build_show_header(json){
    if (json.show_info == null || typeof json.show_info.id == "undefined"){
      return '<div class="show_can"">' +
      '<div class="show_info"><span class="headding nolink">'+t('tvshownamer', 'Unable to find')+' "'+json.name+'"</span></div>' +
      '</div>';
    }
    return '<div class="show_can" data-id="'+json.show_info.id+'">' +
    '<a href="https://www.themoviedb.org/tv/'+json.show_info.id+'" target="_blank"><img class="poster" height="150px" src="'+json.show_info.img_path+'" alt="'+t('tvshownamer', '{show_name} poster, click to open the show in a new window', {show_name: json.show_info.name})+'" title="'+t('tvshownamer', 'Open {show_name} on {website}', {show_name: json.show_info.name, website: 'themoviedb.org'})+'"/></a>' +
    '<div class="show_info"><a href="https://www.themoviedb.org/tv/'+json.show_info.id+'" target="_blank" class="headding">'+json.show_info.name+'</a> <span class="air_date">('+json.show_info.first_air_date.substring(0,4)+')</span>' +
    '' +
    '<p>'+json.show_info.overview+'</p>'+
    '<div class="not_this">'+t('tvshownamer', 'Not this one?')+' <button class="primary" id="next_title" data-show_index="'+json.show_index+'" data-path="'+json.path+'">'+t('tvshownamer', 'Next')+'</button></div></div>' +
    '</div>';
  }
  function build_file_list_header(){
    return '<thead><tr>' +
    '<th class="selection"><input id="select-all" type="checkbox" class="selectCheckBox checkbox select-all"><label for="select-all"><span class="hidden-visually">'+t('tvshownamer', 'Select All')+'</span></label></th>' +
    '<th class="file-name">'+t('tvshownamer', 'File Name')+'</th>' +
    '<th class="buttons"><div class="hidden" id="selected-button"><button class="primary" id="update-all">'+t('tvshownamer', 'Update Selected')+'</button></div></th>' +
    '</tr></thead>';
  }
  function build_file_list(item, hide){
    var tk = '<input id="select-files-'+item.file_id+'" data-fileid="'+item.file_id+'" data-filepath="'+item.path+'" type="checkbox" class="selectCheckBox checkbox select-file"><label for="select-files-'+item.file_id+'"><span class="hidden-visually">'+t('tvshownamer', 'Select')+'</span></label>';
    var tb = '<button class="primary" id="confirm" data-fileid="'+item.file_id+'" data-filepath="'+item.path+'">'+t('tvshownamer', 'Update')+'</button>';
    var tn = '<span class="from">'+item.name+'</span> > <span class="to">'+item.new_name+'</span>';
    var match = 'false';
    var hideit = '';
    if (item.name === item.new_name){tk = '<div class="icon-checkmark"></div>'; tb = '';tn = '<span class="to">'+item.name+'</span>'; match= match ? 'true' : 'false'; hideit = hide ? ' hidden' : '';}
    if (item.new_name == '' ||  item.new_name === undefined){tk = '<div class="icon-unknown" title="'+t('tvshownamer', 'Episode not found')+'"></div>'; tb = '';tn = '<span class="from">'+item.name+'</span>';}
    return '<tr class="file'+hideit+'" data-fileid="'+item.file_id+'" data-filepath="'+item.path+'" data-match="'+match+'" id="file'+item.file_id+'">'+
    '<td class="selection">' + tk + '</td>' +
    '<td class="name">'+tn+'</td>'+
    '<td class="buttons" align="right">' + tb + '</td>' +
    '</tr>';
  }
  function hide_header(tof) {
    if(tof){
      header.style.display = 'none';
    }else{
      header.style.display = 'block';
    }
  }
  function hide_loading(tof) {
    if(tof){
      loader.style.display = 'none';
    }else{
      loader.style.display = 'block';
    }
  }
  function select_all() {
    if($('input.select-all').is(":checked")){
      $('input.select-file').each(function() {
        $(this).prop('checked', true);
      });
    }else{
      $('input.select-file').each(function() {
        $(this).prop('checked', false);
      });
    }
    select_file();
  }
  function update_file_list(){
    var hide_matching = $('#hide_matching').prop('checked');
    $('.file').each(function(i) {
      if (hide_matching && $(this).attr('data-match') == 'true') {
        $(this).addClass('hidden');
      }else {
        $(this).removeClass('hidden');
      }
    });

  }
  function next_title(t){
    get_data('scan', {'scan_folder' : $(t).data('path'), 'show_index' : $(t).data('show_index'), 'datasource': datasource}, render);
  }
  function source_select(t){
    $('.source_button').each(function(i) {
      if ($(this).hasClass('active')) {
        $(this).removeClass('active');
      }
    });
    $(t).addClass('active');
  }
  function rename_file(t){
    var id = $(t).data('fileid');
    var file_path = $(t).data('filepath');
    $('#file'+id+' .selection').html('<div class="icon-loading-small"></div>');
    $(t).css("visibility", "hidden");
    $(t).removeClass('primary');
    get_data('rename', {'file_id' : id, 'new_name' : $('#file'+id+' .to').text(), 'file_path' : file_path}, render, false);
  }
  function select_file(){
    var s = $('input.select-file').filter(':checked').length;
    if (s == 0){
      $('.file_list .file-name').html('File Name');
      $('.file_list #selected-button').addClass('hidden');
    }else{
      $('.file_list .file-name').html(s +' Selected');
      $('.file_list #selected-button').removeClass('hidden');
    }
  }
  function submit_selected(){
    $('.file_list #select-all').css("visibility", "hidden");
    $('.file_list #update-all').css("visibility", "hidden");
    $('.file_list .file-name').html("Updateing...");
    var list = $('input.select-file').filter(':checked');
    var selected =  new Array();
    var i=0;
    list.each(function() {
      var id = $(this).data('fileid');
      var file_path = $(this).data('filepath');
      selected.push({
        'id': id,
        'file_name': file_path});
      $('#file'+id+' .selection').html('<div class="icon-loading-small"></div>');
      $('#file'+id+' #confirm').css("visibility", "hidden");
      $('#file'+id+' #confirm').removeClass('primary');
      i++;
    });
    setTimeout(submit_selected_items, 100, selected ,0);
  }
  function submit_selected_items(items, i){
    if (i < items.length){
      if (current_posts < 6){
        var id = items[i].id;
        var file_path = items[i].file_name;
        get_data('rename', {'file_id' : id, 'new_name' : $('#file'+id+' .to').text(), 'file_path' : file_path}, render, false);
        i++;
      }
      setTimeout(submit_selected_items, 50, items ,i);
    }else{
      select_file();
      $('select-all').css("visibility", "visable");
    }
}
function scanFolderCallback(path){
  if (datasource == ''){
    $(".source_button").each(function(i, el) {
      datasource = $(el).data('source');
    });
  }
  get_data('scan', {'scan_folder' : path, 'datasource': datasource}, render);
}
function setSelectedValue(selectId, valueToSet) {
  var selectObj = document.getElementById(selectId);
  for (var i = 0; i < selectObj.options.length; i++) {
    if (selectObj.options[i].value == valueToSet) {
          selectObj.options[i].selected = true;
          return;
      }
  }
}
  function set_active_datasource(ds){
    if ($('#source_tvdb').hasClass('active')) {
      $('#source_tvdb').removeClass('active');
    }
    if ($('#source_tmdb').hasClass('active')) {
      $('#source_tmdb').removeClass('active');
    }
    if (ds == 'tvdb'){
      $('#source_tvdb').addClass("active");
    }
    if (ds == 'tmdb'){
      $('#source_tmdb').addClass("active");
    }
  }
  function validate_settings(e){
    if (!$('#enable_tmdb').is(':checked') && !$('#enable_tvdb').is(':checked') ){
      simple_message(t('tvshownamer', 'Unable to disable both data sources'));
      $(e).prop('checked', true);
    }
    if (!$('#enable_tmdb').is(':checked')){
      $('#source_tmdb').addClass("hide");
      if ($('#source_tmdb').hasClass('active')) {
        $('#source_tmdb').removeClass('active');
        $('#source_tvdb').addClass("active");
      }
    }else{
      $('#source_tmdb').removeClass("hide");
    }
    if (!$('#enable_tvdb').is(':checked')){
      $('#source_tvdb').addClass("hide");
      if ($('#source_tvdb').hasClass('active')) {
        $('#source_tvdb').removeClass('active');
        $('#source_tmdb').addClass("active");
      }
    }else{
      $('#source_tvdb').removeClass("hide");
    }
  }
  function get_data(url, perams, callback, l = true){
    current_posts++;
    if (perams === undefined || perams === null){
     $.ajax({
      url: baseUrl + '/' + url,
      dataType: "json",
      beforeSend: function() {if(l){hide_loading(false);}},
      success: function(data) {callback(data);},
      complete: function() {if(l){hide_loading(true);}current_posts--;}
     });
   }else{
     $.ajax({
      url: baseUrl + '/' + url,
      dataType: "json",
      type: "POST",
      data: JSON.stringify(perams),
      beforeSend: function() {if(l){hide_loading(false);}},
      success: function(data) {callback(data);},
      complete: function() {if(l){hide_loading(true);}current_posts--;}
     });
   }
  }
  $("#scanfolder").on("click", function(e) {
    OC.dialogs.filepicker('Select folder to scan', scanFolderCallback, false, ['httpd/unix-directory'] , false, OC.dialogs.FILEPICKER_TYPE_CHOOSE);
  });
  $("#tmdb_api_key").focusout(function(e) {
    get_data('save_setting', {'setting' : 'tmdb_api_key', 'data' : $('#tmdb_api_key').val()}, message, false);
  });
  $("#file_name_structure").focusout(function(e) {
    get_data('save_setting', {'setting' : 'file_name_structure', 'data' : $('#file_name_structure').val()}, message, false);
  });
  $(document).ready(function () {
    setSelectedValue("preferred_language", $('#preferred_language').data('selected-value'));
    $("#preferred_language").change(function(e) {
      var lang_exe = function (d) {message(d); $('#rescan').click();};
      get_data('save_setting', {'setting' : 'preferred_language', 'data' : $('#preferred_language').val()}, lang_exe, false);
    });
  });
  $(".setting_toggle").change(function(e) {
    validate_settings(this);
    get_data('save_setting', {'setting' : $(this).data('setting'), 'data' : $(this).prop('checked') ? "checked" : ""}, message, false);
    update_file_list();
  });
  $("#dismiss").on("click", function(e) {
    var id = $(e).data('id');
    get_data('save_setting', {'setting' : 'hide_message', 'data' : id}, undefined, true);
    $('.message#'+id).css("visibility", "hidden");
  });
  $("#exicute").on("click", function(e) {
    var id = $(e).data('id');
    get_data('execute', {'message' : id}, reload_page, true);
  });
  $(".source_button").on("click", function(e) {
    source_select(this);
    datasource = $(this).data('source');
    get_data('save_setting', {'setting' : $(this).data('setting'), 'data' : $(this).data('source')}, message, false);
    get_data('scan', {'scan_folder' : $('a.reload').data('path'), 'datasource': datasource}, render);
  });
  $("#app-settings-button").on("click", function(e) {
    $('#app-settings-content').toggleClass('open');
  });
  $("#app-datasource-button").on("click", function(e) {
    $('#app-datasource-content').toggleClass('open');
  });
  function reload_page(){
    window.location.reload();
  }
})();
