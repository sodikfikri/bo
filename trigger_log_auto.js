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

console.log("Trigger is running!");
setInterval(function(){
	let beginHour = [1,8,21];
    let endHour = [6,17,23];
    let startHappyHourD = new Date();
    let endHappyHourD = new Date();
	let currentD = new Date();
    let indek = 0;
    isProses = false;
    while(indek < 3)
    {
        startHappyHourD.setHours(beginHour[indek],0,0);
        endHappyHourD.setHours(endHour[indek],0,0);
        
        if(currentD >= startHappyHourD && currentD < endHappyHourD ){
            isProses = true;
        }

        indek++;
    }
	if(isProses === true){
			if(emptyQueue<30){
				stillProcess = 1;
				var xhr = new XMLHttpRequest();
				xhr.withCredentials = true;

				xhr.onreadystatechange = function() {
					if (this.readyState == 4 && this.status == 200) {
						//console.log(this.responseText);
						//console.log(this.responseText);
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
				xhr.open("GET", "https://inact.interactiveholic.net/bo/api/queue/processQueueDB/attendance");
				//xhr.setRequestHeader("User-Agent", "Fiddler");
				//xhr.setRequestHeader("Content-Type", "text/plain");
				xhr.send(data);
			}else{
				// empty return
				//console.log("waiting");
				emptyQueueTollerance = emptyQueueTollerance + 1;
				if(emptyQueueTollerance>500){
					emptyQueue = 0;
				}
			}
	}
}, 5000);
