  function pop(url,width,height){
    popup = window.open(url, 'popup', 'width=' + width + 
                        ',height=' + height + ',scrollbars=no');
    popup.focus();
    return;
  }

  function OKCancel(message){
    input_box=confirm(message);
    
    if (input_box==true){ 
      document.form1.confirmed.value=1;
      form1.submit();
    }else{
      document.form1.confirmed.value=0;
    }
  }
