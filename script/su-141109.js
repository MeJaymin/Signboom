/*
	ajmUpload HTML Upload 
*/

function StartUpload() {
	var myserver = "upload.signboom.com";
	var myport = "31164";
	winOpts = "height=340,width=500,scrollbars=yes,location=no,toolbar=no,menubar=no,resizable=no,status=no";
	//if (document.orderForm.Token) {
	//	Token = document.orderForm.Token.value;}
	//else {
		Token = (new Date()).getTime() % 1000000000;
		//}
	/* Commented by zCon for Testing ************
	window.open("http://" + myserver + ":" + myport + "/progress.html?Token=" + Token, Token, winOpts);
	document.orderForm.action = "http://" + myserver + ":" + myport + "/upload.html?Token=" + Token;
	document.orderForm.submit();
	*/
	
	//window.location.href = "http://10.235.4.47/Signboom/WebServer/orderthanks.php?order=28531&dbToken="+Token+"";
	window.location.href = "http://54.200.138.28/Signboom/orderthanks.php?order=28531&dbToken="+Token+"";
	//alert("File uploaded on upload.signboom.com and orderform submitted.");
}
