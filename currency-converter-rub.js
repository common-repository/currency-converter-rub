/* 

Author: PahaW
Version: 1.2.3

*/

// show, hidden div
function show_hide(id) {
	document.getElementById(id).style.display = document.getElementById(id).style.display == 'none' ? 'block' : 'none';
}

// Edilt fields
function edit_input(obj, name) {
    	 var num=document.getElementsByName(name);
    	 var i=0;
    	 if (obj.checked) {
          for (i=0; i< num.length; i++){
            //document.getElementsByName(name).item(i).removeAttribute("readonly");
            document.getElementsByName(name).item(i).removeAttribute("disabled");
            document.getElementsByName(name).item(i).className = "input2";
          }
       } else {
          for (i=0; i< num.length; i++){
            document.getElementsByName(name).item(i).setAttribute("disabled", "disabled");
            document.getElementsByName(name).item(i).className = "input";
          }
       }
}

// Convert
function summa(theForum, num_res, i){
       
     var number=Number(document.getElementById('number_'+i).value);
     var result=Number(document.getElementById('result_'+i).value);
     
     if (num_res > 1) {
        myVar = (number*result) / num_res;
     } else {
        myVar=number*result;
     }
     myVar2=number_format(myVar); //

     var S = new Array();
     var pt = 0;
     S = myVar2.split(".");
     if (isEmpty(S[1])!=true){ pt = S[1].substr(0, 2); }
     return document.forms[theForum].elements['view_'+i].value=S[0]+'.'+pt;
}

// Convert number format
function number_format(num) {
      var n = Math.floor(num);
      var myNum = num + "";
      var myDec = ""

      if (myNum.indexOf('.',0) > -1){
        myDec = myNum.substring(myNum.indexOf('.',0),myNum.length);
      }

      var arr=new Array('0'), i=0; 
      while (n>0){
          arr[i]=''+n%1000; n=Math.floor(n/1000);
          i++;
      }
      arr=arr.reverse();
      for (var i in arr) if (i>0) //padding zeros
          while (arr[i].length<3) arr[i]='0'+arr[i];
      return arr.join() + myDec;
}

// Start
var timeout = null;
function doLoadUp(theForum, num_res, i) {
    if (timeout) clearTimeout(timeout);
    timeout = setTimeout(summa(theForum, num_res, i), 1000);
           
}

function isEmpty(obj) {
	for(var prop in obj) {
		if(obj.hasOwnProperty(prop))
			return false;
	}
	return true;
}