var XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest;
var xhr = new XMLHttpRequest();
var stillProcess = 0;
var tollerance   = 0;
var emptyQueue   = 0;
var emptyQueueTollerance = 0;
//var endpoinLock  = false;

//inisialisasi jam eksekusi antrian
let isProses;
//end inisialisasi jam eksekusi antrian

console.log("Service generate data PJN!");
setInterval(function(){
    let currentD = new Date();
    let tahun = currentD.getFullYear();
    let bulan = currentD.getMonth();
    let tanggal = currentD.getDate();
    let jam = currentD.getHours();
    let menit = currentD.getMinutes();
    let detik = currentD.getSeconds();
    process.stdout.write(tahun+'-'+bulan+'-'+tanggal+', '+jam+':'+menit+':'+detik);
    process.stdout.write('===============');
    process.stdout.cursorTo(0);
    if(emptyQueue<30 && (tanggal>=6 && tanggal <=9)){
        stillProcess = 1;
        var xhr = new XMLHttpRequest();
        xhr.withCredentials = true;

        xhr.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText=="OK") {
                    stillProcess = 0;

                }else if(this.responseText=="noqueue"){
                    emptyQueue = emptyQueue + 1;
                    if(emptyQueue>=30){
                        emptyQueueTollerance = 0;
                    }
                }
            }
        };
        data = "";
        xhr.open("GET", "https://interactive.co.id/mydashboard/risetjurnal/iboss_pjn_auto.php");
        xhr.send(data);
        
    }else{
        // empty return
        //console.log("waiting");
        emptyQueueTollerance = emptyQueueTollerance + 1;
        if(emptyQueueTollerance>500){
            emptyQueue = 0;
        }
        
    }
}, 600000);
