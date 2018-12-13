<?php 
  include ('authadmin.php'); 


  function uploadFile()
  {
    // We store all PDF files containing info on product or finishing options in the same folder.
    // www.signboom.com/product-files
    $root_folder = $_SERVER['DOCUMENT_ROOT'];
    $full_path = $root_folder . '/product-files';

    if ($_FILES['file_to_upload']['error'] == UPLOAD_ERR_OK)
    {
      $file_name = $_FILES['file_to_upload']['name'];
      $file_tmp_name = $_FILES['file_to_upload']['tmp_name'];
      // TO DO: Check that the file is really a PDF file using finfo_open function.

      // Strip off the directory path from the filename.
      $original_filename = trim(basename($file_name));
      // Remove any characters that are illegal on any of the three major operating systems. 
      // The preg replaces all characters up through space and all past ~ along with 
      // the above reserved characters. 
      $reserved = preg_quote('\/:*?"<>|', '/');
      /*$filename = preg_replace_callback("/([\\x00-\\x20\\x7f-\\xff{$reserved}])/e", "_", $original_filename);*/
      $filename = preg_replace_callback("/([\\x00-\\x20\\x7f-\\xff{$reserved}])/", "_", $original_filename);

      // If that file is not yet saved on the server, save it. 
      //echo $full_path; die;
      $target_path = $full_path . '/' . $filename;
	     //echo $target_path; die;
      $error_message = "";
      if (file_exists($target_path))
      {
        // That file is already on the server.
        $error_message .= 'The file ' . $filename . ' is already on the web server.';
      }
      else
      {
        // Copy the file into permanent storage. 
        $success = move_uploaded_file($file_tmp_name, $target_path);
        if (!$success) 
        {
          $error_message .= ' There was an error uploading the file ' .  $original_filename . '.';
          switch ($_FILES['file_to_upload']['error'])
          {  
            case 1:
              $error_message .= ' The file is bigger than this PHP installation allows.';
              break;
            case 2:
              $error_message .= ' The file is bigger than this form allows.';
              break;
            case 3:
              $error_message .= ' Only part of the file was uploaded. Please try again.';
              break;
            case 4:
              $error_message .= ' No file was uploaded.';
              break;
          }
        }
      }
      return $error_message;
    } // end of if no error for this file
    return '';  // $_FILES['file_to_upload']['error'];
  }

  /********************************* Start *******************************/

  $error_message = '';
  if (isset($_POST['upload_now']))
  {
    if (empty($_FILES['file_to_upload']['name']))
    {
      $message = 'Please choose a file to upload.';
    }
    else
    {
      $message = uploadFile();
      if (strlen(trim($message)) == 0)
        $message = 'Your file has been uploaded.';
    }
  }
  
  // Display the parameters
  include ('templates/files.php'); 
  
  /* Free memory. */
  /*((mysqli_free_result($result) || (is_object($result) && (get_class($result) == "mysqli_result"))) ? true : false);*/
?>

