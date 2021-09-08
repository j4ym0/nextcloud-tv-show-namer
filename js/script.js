(function () {
  var baseUrl = OC.generateUrl('/apps/tvshownamer');
  var can = document.getElementById('display-can');
  var loader = document.getElementById('loading-can');
  var header = document.getElementById('headding');
  var checkElForCallback = function(el, callback) {
    if ($(el).length) {
      $(el).each(function() {$(this).click(function() {callback($(this))})});
//    dont need this at the mo
//    } else {
//      setTimeout(function() { checkElForCallback(el, callback);}, 100);
    }
  };


  function render(data) {
    if (data.success){
      if (data.action === "update"){
        $('#'+data.element).replaceWith(build_file_list(data.file[0]));
      }else{
        var r = '';
        r += build_show_header(data);
        r += '<table class="file_list">';
        r += build_file_list_header();
        for(var i in data.files) {
          r += build_file_list(data.files[i]);
        }
        r+='</table>';
        can.innerHTML=r;
        checkElForCallback('button#confirm', function(t){rename_file(t);});
        checkElForCallback('button#next_title', function(t){next_title(t);});
        $('.current_folder').html('<a href="../files/?dir=' + data.path + '">' + data.path + '</a><a data-path="'+data.path +'" class="reload" alt="Rescan Folder"></a>');
        checkElForCallback('a.reload', function(t){  get_data('scan', {'scan_folder' : $(t).data('path')}, render);});
      }
    }else{
      message(data);
    }
  }
  function message(json = null){
    OC.Notification.showTemporary(json.message != null ? json.message : 'Unexpected error, Sorry');
  }
  function build_show_header(json){
    if (json.show_info == null){
      return '<div class="show_can"">' +
      '<div class="show_info"><span class="headding">Unable to find "'+json.name+'"</span><div class="not_this">Not this one? <button class="primary" id="next_title"  data-show_index="'+json.show_index+'">Next</button></div></div>' +
      '</div>';
    }
    return '<div class="show_can" data-id="'+json.show_info.id+'">' +
    '<img class="poster" height="150px" src="image'+json.show_info.poster_path+'" alt="'+json.show_info.name+' poster"/>' +
    '<div class="show_info"><a href="https://www.themoviedb.org/tv/'+json.show_info.id+'" target="_blank" class="headding">'+json.show_info.name+'</a> <span class="air_date">('+json.show_info.first_air_date.substring(0,4)+')</span><p>'+json.show_info.overview+'</p>'+
    '<div class="not_this">Not this one? <button class="primary" id="next_title" data-show_index="'+json.show_index+'" data-path="'+json.path+'">Next</button></div></div>' +
    '</div>';
  }
  function build_file_list_header(){
    return '<tr>' +
    '<th class="selection"></th>' +
    '<th class="name">File Name</th>' +
    '<th class="buttons"></th>' +
    '</tr>';
  }
  function build_file_list(item){
    var tk = '<input id="select-files-'+item.file_id+'" type="checkbox" class="selectCheckBox checkbox"><label for="select-files-'+item.file_id+'"><span class="hidden-visually">Select</span></label>';
    var tb = '<button class="primary" id="confirm" data-fileid="'+item.file_id+'" data-filepath="'+item.path+'">Update</button>';
    var tn = '<span class="from">'+item.name+'</span> > <span class="to">'+item.new_name+'</span>';
    if (item.name === item.new_name){tk = '<div class="icon-checkmark"></div>'; tb = '';tn = '<span class="to">'+item.name+'</span>';}
    if (item.new_name == '' ||  item.new_name === undefined){tk = '<div class="icon-unknown" title="Episode not found"></div>'; tb = '';tn = '<span class="from">'+item.name+'</span>';}
    return '<tr class="file" data-fileid="'+item.file_id+'" id="file'+item.file_id+'">'+
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
  function next_title(t){
    get_data('scan', {'scan_folder' : $(t).data('path'), 'show_index' : $(t).data('show_index')}, render);
  }
  function rename_file(t){
    var id = $(t).data('fileid');
    var file_path = $(t).data('filepath');
    $('#file'+id+' .selection').html('<div class="icon-loading-small"></div>');
    $(t).css("visibility", "hidden");
    $(t).removeClass('primary');
    get_data('rename', {'file_id' : id, 'new_name' : $('#file'+id+' .to').text(), 'file_path' : file_path}, render, false);
  }


function scanFolderCallback(path){
  get_data('scan', {'scan_folder' : path}, render);
}


  function get_data(url, perams, callback, l = true){
    if (perams === undefined || perams === null){
     $.ajax({
      url: baseUrl + '/' + url,
      dataType: "json",
      beforeSend: function() {if(l){hide_loading(false);}},
      success: function(data) {callback(data);},
      complete: function() {if(l){hide_loading(true);}}
     });
   }else{
     $.ajax({
      url: baseUrl + '/' + url,
      dataType: "json",
      type: "POST",
      data: JSON.stringify(perams),
      beforeSend: function() {if(l){hide_loading(false);}},
      success: function(data) {callback(data);},
      complete: function() {if(l){hide_loading(true);}}
     });
   }
  }
  $("#scanfolder").on("click", function(e) {
    OC.dialogs.filepicker('Select folder to scan', scanFolderCallback, false, ['httpd/unix-directory'] , false, OC.dialogs.FILEPICKER_TYPE_CHOOSE);
  });
  $("#tmdb_api_key").focusout(function(e) {
    get_data('save_setting', {'setting' : 'tmdb_api_key', 'data' : $('#tmdb_api_key').val()}, message, false);
  });
})();
