$(document).ready(function(){
  $("#country").change(function(){
    var countryID = $(this).val();
    loadProvince(countryID);
  });
  $("#province").change(function(){
    alert("lol");
    var inputprovinceID = $(this).val();
    loadCity(inputprovinceID);

  });
  $("#btnSave").click(function(){
    updateSetting();

  });
});

function loadProvince(countryID){
  if(countryID=="Indonesia"){
    $('#component-province').html('<select onchange="loadCity($(this).val())" name="province" class="form-control" id="province" ><option value="">Select Province</option></select>');
    $('#component-city').html('<select  name="city" class="form-control" id="city"><option value="">Select City</option></select>');
    $("#province").html('<option value="">Select Province</option>');
    $.ajax({
      method : "POST",
      url    : url + "regionGetProvince",
      data   : {countryID:countryID},
      success: function(res){
        var arrProvice = JSON.parse(res);
        arrProvice.forEach(function(row,index){
          if(row.name==provinceID){
            var selected = 'selected';
          }else{
            var selected = '';
          }
          $("#province").append('<option '+selected+' value="'+row.name+'">'+row.name+'</option>');
        });
      }
    });

  }else{
    $('#component-province').html('<input data-validation-engine="validate[custom[onlyLetterNumber]]" value="'+provinceID+'" type="text" name="province" class="form-control" id="province" >');
    $('#component-city').html('<input data-validation-engine="validate[custom[onlyLetterNumber]]" value="'+cityID+'" type="text" name="city" class="form-control" id="city">');
    
  }
}

function loadCity(inputprovinceID){
  $("#city").html('<option value="">Select City</option>');
  $.ajax({
    method : "POST",
    url    : url + "regionGetCity",
    data   : {provinceID:inputprovinceID},
    success: function(res){
      var arrCity = JSON.parse(res);
      arrCity.forEach(function(row,index){
        if(cityID==row.name){
          var selected = 'selected';
        }else{
          var selected = '';
        }
        $("#city").append('<option '+selected+' value="'+row.name+'">'+row.name+'</option>');
      });
    }
  });
}

function updateSetting(){
  var companyName     = $("#companyName").val();
  var country     = $("#country").val();
  var province      = $("#province").val();
  var city      = $("#city").val();
  var address     = $("#address").val();
  var telp      = $("#telp").val();
  var email     = $("#email").val();
  var website     = $("#website").val();
  var companysize     = $("#companysize").val();
  var date_start     = $("#date_start").val();
  var date_end     = $("#date_end").val();
  var access     = $("input[name='access[]']:checked").map(function(){return $(this).val();}).get();
  var strAccess = "";
  for (var i = 0; i < access.length; i++) {
	   strAccess = strAccess+access[i]+"|";
	}

  $.ajax({
    method : "POST",
    url    : url + "companySettingUpdate",
    data   : {companyName:companyName,country:country,province:province,city:city,address:address,telp:telp,email:email,website:website,companysize:companysize,date_start:date_start,date_end:date_end,access:strAccess},
    success: function(res){
      if(res=="success"){
        $("#setting-msg").html('<div class="callout callout-success">'+
                               '<h4>Success</h4>'+
                               '<p>Your Setting Was Updated!</p>'+
                               '</div>');
      }else{
        $("#setting-msg").html('<div class="callout callout-danger">'+
                               '<h4>Failed</h4>'+
                               '<p>Your Setting Cannot Be Update!</p>'+
                               '</div>');
      }
    }
  });
}
