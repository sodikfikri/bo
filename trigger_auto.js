var XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest;
var xhr = new XMLHttpRequest();
var stillProcess = 0;
var tollerance   = 0;
var emptyQueue   = 0;
var emptyQueueTollerance = 0;
//var endpoinLock  = false;

//inisialisasi jam eksekusi antrian
//let beginHour = [8,13,18];
//let endHour = [11,15,22];

//end inisialisasi jam eksekusi antrian

console.log("Trigger is running!");
setInterval(function(){
	let beginHour = [0,8,13,21];
	let endHour = [5,11,17,23];
	let isProses;
	let jenisEndpoint;
	let indek = 0;

	let currentD = new Date();
	let startHappyHourD = new Date();
	let endHappyHourD = new Date();

	if(indek >= 4) { indek = 0; }
    isProses = false;
		jenisEndpoint = 'https://inact.interactiveholic.net/bo/api/queue/processQueueDB/attendance';
    while(indek < 4)
    {
        startHappyHourD.setHours(beginHour[indek],0,0);
        endHappyHourD.setHours(endHour[indek],0,0);
        
        if(currentD >= startHappyHourD && currentD < endHappyHourD ){
            isProses = true;
						if(beginHour[indek] == 0 || beginHour[indek] == 21 ) {
							jenisEndpoint = 'https://inact.interactiveholic.net/bo/api/queue/processQueueDB';	
						}
        }

        indek++;
    }
    if(stillProcess==1 && tollerance>100){
		stillProcess = 0;
	}else{
		tollerance = tollerance + 1;
	}
	if(isProses === true && stillProcess===0){
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
				xhr.open("GET", jenisEndpoint);
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
}, 3000);
