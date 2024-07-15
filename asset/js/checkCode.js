function checkExists(CodeId,notifID,endPointUrl,ExistsMessage,notExistsMessage,entityID){
  var code = $("#"+CodeId).val();
  $.ajax({
    method : 'POST',
    url    : url + endPointUrl,
    data   : {code:code,entityID:entityID},
    success: function(res){
      if(code==""){
        $("#"+notifID).html('');
      }else{
        if(res=="exists"){
          $("#"+CodeId).val("");
          $("#"+notifID).html('<span class="text-red">'+ExistsMessage+'</span>');
        }else{
          $("#"+notifID).html('<span class="text-green">'+notExistsMessage+'</span>');
        }
      }
    }
  });
}
