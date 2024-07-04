const fs = require("fs");

const oldName_1 = 'C:/xampp/htdocs/inact/fp_';
const newName_1 = 'C:/xampp/htdocs/inact/fp';

const oldName_2 = 'C:/xampp/htdocs/inact/fp';
const newName_2 = 'C:/xampp/htdocs/inact/fp_';

function addZero(i) {
    if (i < 10) {i = "0" + i}
    return i;
}
//const part1_startime = '08:00';
//const part1_endtime = '10:00';
const part1_startime = '03:00';
const part1_endtime = '08:00';

const part2_startime = '12:00';
const part2_endtime = '14:00';

console.log("Service rename FP");
setInterval(function(){
    const d = new Date();
    let h = addZero(d.getHours());
    let m = addZero(d.getMinutes());
    let time = h + ":" + m;

    if (part1_startime <= time && part1_endtime >= time) {
        if(fs.existsSync(oldName_2)) {
            fs.rename(oldName_2, newName_2, function(err) {
                if (err) {
                  console.log(time + "->Error rename");
                } else {
                  console.log(time + "->Successfully renamed the directory to fp_.");
                }
              })
        }
    }
    else if (part2_startime <= time && part2_endtime >= time) {
        if(fs.existsSync(oldName_2)) {
            fs.rename(oldName_2, newName_2, function(err) {
                if (err) {
                  console.log(time + "->Error rename");
                } else {
                  console.log(time + "->Successfully renamed the directory fp_.");
                }
              })
        }
    }
    else {
        if(fs.existsSync(oldName_1)) {
            fs.rename(oldName_1, newName_1, function(err) {
                if (err) {
                  console.log(time + "->Error rename");
                } else {
                  console.log(time + "->Successfully renamed the directory fp.");
                }
              })
        }
    }
}, 60000);