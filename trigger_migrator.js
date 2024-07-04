var XMLHttpRequest = require("xmlhttprequest").XMLHttpRequest;
var stillProcess = 0;
//var endpoinLock  = false;
console.log("Trigger is running every 100 second!");
setInterval(function () {
	var xhr = new XMLHttpRequest();
	xhr.withCredentials = true;
	xhr.onreadystatechange = function () {
		// console.log(this.status);
	};
	data = "";
	//xhr.open("GET", "localhost/inact-projects/api/queue/processQueueDB");
	xhr.open(
		"GET",
		"http://inact.interactiveholic.net/bo/dev/migrator/migrateToFinalAttendance"
	);
	xhr.send(data);
}, 95000);
