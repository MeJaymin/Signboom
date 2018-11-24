  function allowOnlyJPGorPDF()
  {
    var filename = document.getElementById('file_to_upload').value;
    var file_valid = true;
    if (filename.length > 0)
    {
      filename = filename.toLowerCase();
      position = filename.lastIndexOf(".pdf");
      if (position != filename.length - 4) 
      {
        position = filename.lastIndexOf(".jpg");
        if (position != filename.length - 4) 
	{
	  alert("That file cannot be uploaded. Only PDF and JPG files may be uploaded.");
          file_valid = false;
	}
      }
    }
    return(file_valid);
  }

