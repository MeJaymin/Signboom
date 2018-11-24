// JavaScript Document
var popUpWin=0;
/*
function strltrim() {
	//Match spaces at beginning of text and replace with a null string
    return this.replace(/^\s+/,'');
}
function strrtrim() {
//Match spaces at end of text and replace with a null string
	return this.replace(/\s+$/,'');
}
function strtrim() {
//Match spaces at beginning and end of text and replace
//with null strings
	return this.replace(/^\s+/,'').replace(/\s+$/,'');
}
String.prototype.ltrim = strltrim;
String.prototype.rtrim = strrtrim;
String.prototype.trim = strtrim;
*/

function strltrim(a) {
	//Match spaces at beginning of text and replace with a null string
    return a.replace(/^\s+/,'');
}
function strrtrim(a) {
//Match spaces at end of text and replace with a null string
	return a.replace(/\s+$/,'');
}
function strtrim(a) {
//Match spaces at beginning and end of text and replace
//with null strings
	return a.replace(/^\s+/,'').replace(/\s+$/,'');
}

function popUpWindow(URLStr, left, top, width, height)
{
  if(popUpWin)
  {
    if(!popUpWin.closed) popUpWin.close();
  }
  popUpWin = open(URLStr, 'popUpWin', 'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbar=no,resizable=no,copyhistory=yes,width='+width+',height='+height+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
}
function messageWindow(title, msg)
{
  var width="300", height="125";
  var left = (screen.width/2) - width/2;
  var top = (screen.height/2) - height/2;
  var styleStr = 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbar=no,resizable=no,copyhistory=yes,width='+width+',height='+height+',left='+left+',top='+top+',screenX='+left+',screenY='+top;
  var msgWindow = window.open("","msgWindow", styleStr);
  var head = '<head><title>'+title+'</title></head>';
  var body = '<center>'+msg+'<br><p><form><input type="button" value="   Done   " onClick="self.close()"></form>';
  msgWindow.document.write(head + body);
}
// Example:
// value1 = 3; value2 = 4;
// messageBox("text message %s and %s", value1, value2);
// this message box will display the text "text message 3 and 4"
function messageBox()
{
  var i, msg = "", argNum = 0, startPos;
  var args = messageBox.arguments;
  var numArgs = args.length;
  if(numArgs)
  {
    theStr = args[argNum++];
    startPos = 0;  endPos = theStr.indexOf("%s",startPos);
    if(endPos == -1) endPos = theStr.length;
    while(startPos < theStr.length)
    {
      msg += theStr.substring(startPos,endPos);
      if (argNum < numArgs) msg += args[argNum++];
      startPos = endPos+2;  endPos = theStr.indexOf("%s",startPos);
      if (endPos == -1) endPos = theStr.length;
    }
    if (!msg) msg = args[0];
  }
  alert(msg);
}
