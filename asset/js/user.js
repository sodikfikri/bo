
function checkEmail(Emailid,notifID,userid){
  var email = $("#"+Emailid).val();
  $.ajax({
    method : 'POST',
    url    : url + "check-email-exists",
    data   : {email:email,userid:userid},
    success: function(res){
      if(res=="exists"){
        $("#"+Emailid).val("");
        $("#"+notifID).html('<span class="text-red">Email is Exists! Use Another Email</span>');
      }else{
        $("#"+notifID).html('<span class="text-green">Email is Available</span>');
      }

    }
  });
}

function checkPhone(Phoneid,notifID,userid){
  var phone = $("#"+Phoneid).val();
  $.ajax({
    method : 'POST',
    url    : url + "check-phone-exists",
    data   : {phone:phone,userid:userid},
    success: function(res){
      if(phone==""){
        $("#"+notifID).html('');
      }else{
        if(res=="exists"){
          $("#"+Phoneid).val("");
          $("#"+notifID).html('<span class="text-red">Phone is Exists! Use Another Phone</span>');
        }else{
          $("#"+notifID).html('<span class="text-green">Phone is Available</span>');
        }
      }

    }
  });
}

function delUser(userID){
  Swal.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    type: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Yes, delete it!'
  }).then((result) => {
    if (result.value) {
      window.open(url + 'delete-user/' + userID,'_self');
    }
  })
}
