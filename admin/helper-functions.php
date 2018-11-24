<?php

/*********************************************************/
function isInteger($value)
{
  if (preg_match('/^\d+$/',$value)) 
    return true;
   else 
    return false;
}

/*********************************************************/
/* To remove the slashes that mysql_real_escape_string   */
/* puts in, so they don't accumulate over time.          */
/*********************************************************/
function bbCode($text)
{
  $text = htmlspecialchars($text, ENT_QUOTES, 'UTF-8');

  // Convert Windows (\r\n) to Unix (\n)
  $text = str_replace("\\r\\n", "\\n", $text);
  // Convert Macintosh (\r) to Unix (\n)
  $text = str_replace("\\r", "\\n", $text);

  // Line breaks
  $text = str_replace("\\n", '<br>', $text);

  $text = str_replace("\\", "", $text);

  return $text;
}

/*********************************************************/
function isValidProductId($text)
{
  if (strlen($text) < 3) 
    return false;

  /* Allow only numbers. */
  if (preg_match("/^[0-9]*$/", $text))
    return true;
  else
    return false;
}

/*********************************************************/

function isValidPolicyId($text)
{
  /* Allow only numbers. */
  if (preg_match("/^[0-9]*$/", $text))
    return true;
  else
    return false;
}

/*********************************************************/
function isValidFinishingOptionId($text)
{
  if (strlen($text) < 2) 
    return false;

  /* Allow only numbers. */
  if (preg_match("/^[0-9]*$/", $text))
    return true;
  else
    return false;
}

/*********************************************************/
function isValidProductCode($text)
{
  global $DBConn;

  if ((strlen($text) < 2) || (strlen($text) > 5 ))
    return false;

  /* Allow only letters and numbers. */
  if (!preg_match("/^[A-Z0-9]*$/", $text))
    return false;

  // Make sure that name is not already in use.
  $query = "SELECT Id FROM signboom_allproducts WHERE Code = '$text'";
  $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $count = mysqli_num_rows($result);
  if ($count > 0)
    return false;
  else
    return true;
}

/*********************************************************/
function isValidOrderId($text)
{
  global $DBConn;

  $text = trim($text);

  if (!preg_match('/^\d+$/',$text)) 
    return false;

  $query = "SELECT ID FROM signboom_ordermast WHERE ID = '$text'";
  $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $count = mysqli_num_rows($result);
  if ($count > 0)
    return true;
  else
    return false;
}

/*********************************************************/
function isValidQueue($text)
{
  if (($text != 'Print') && ($text != 'Lam') && ($text != 'Kiss') && ($text != 'CNC') && ($text != 'Finish') && ($text != ''))
    return false;
  else
    return true;
}

/*********************************************************/
function isValidLaminate($text)
{
  global $DBConn;

  if ((strlen($text) < 2) || (strlen($text) > 5 ))
    return false;

  /* Allow only letters and numbers. */
  if (!preg_match("/^[A-Z0-9]*$/", $text))
    return false;

  // Make sure that laminate exists in the product table of the database
  $query = "SELECT Id FROM signboom_allproducts WHERE Code = '$text' AND Category = 'LAMINATE'";
  $result = mysqli_query( $DBConn, $query) or die(mysqli_error($GLOBALS["___mysqli_ston"]));
  $count = mysqli_num_rows($result);
  if ($count > 0)
    return true;
  else
    return false;
}

/*********************************************************/
function isValidProductName($text)
{
  if ((strlen($text) < 4) || (strlen($text) > 64 ))
    return false;

  /* Allow only letters, numbers, spaces and -./() */
  if (preg_match("/^[a-zA-Z0-9\s-.\/()]*$/", $text))
    return true;
  else
    return false;
}
 
/*********************************************************/
function isValidDimension($text)
{
  if (strlen($text) == 0) 
    return false;

  if (is_numeric($text))
    return true;
  else
    return false;
}

/*********************************************************/
function isValidCostFactor($text)
{
  if (strlen($text) == 0) 
    return false;

  if (is_numeric($text))
    return true;
  else
    return false;
}

/*********************************************************/
function isValidSortGroup($text)
{
  if (strlen($text) == 0) 
    return false;

  /* Allow only capital letters. */
  if (preg_match("/^[A-Z]*$/", $text))
    return true;
  else
    return false;
}

/*********************************************************/
function isValidSortOrder($text)
{
  if (strlen($text) == 0) 
    return false;

  if (ctype_digit($text))
    return true;
  else
    return false;
}

/*********************************************************/
function isValidFinishingOptionCode($text)
{
  if (strlen($text) == 0) 
    return false;

  /* Allow only XX-X or XX-XX or XX-XXX formats where X is a letter or (in one position) a number. */
  //if ((preg_match("/^[A-Z]{2}-[A-Z]{1}$/", $text)) || (preg_match("/^[A-Z]{2}-[A-Z]{2}$/", $text)))
  if ((preg_match("/^[A-Z]{2}-[A-Z]{1}$/", $text)) || 
      (preg_match("/^[A-Z]{2}-[A-Z0-9]{1}[A-Z]{1}$/", $text)) || 
      (preg_match("/^[A-Z]{2}-[A-Z0-9]{1}[A-Z]{2}$/", $text)) 
     )
    return true;
  else
    return false;
}
/*********************************************************/

function isValidPhoneNumber($phone)
{
  if(!preg_match("/^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/", $phone))
/*** /^([1]-)?[0-9]{3}-[0-9]{3}-[0-9]{4}$/i", $phone)) ****/
    return false;
  else
    return true;
}
 
function isValidEmailAddress($email)
{
  if(!preg_match("/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/", $email))
/*** /^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/i", $email)) ****/
    return false;
  else
    return true;
}

function isValidURL($url)
{
  $regex = "((https?|ftp)\:\/\/)?"; // Scheme
  $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?"; // User and Pass
  $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})"; // Host or IP
  $regex .= "(\:[0-9]{2,5})?"; // Port
  $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?"; // Path
  $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?"; // GET Query
  $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?"; // Anchor

  if (!preg_match("/^$regex$/", $url))
    return false;
  else
    return true;
}
function isValidPostalCode($postal_code)
{
  if (preg_match("/^[a-zA-Z]{1}\d{1}[a-zA-Z]{1} \d{1}[a-zA-Z]{1}\d{1}$/", $postal_code))
    return true;
  else
    return false;
}
// From this web page: http://roshanbh.com.np/2008/03/usa-zip-code-format-validation-php.html
function isValidZipCode($zip_code)
{
 if (preg_match("/^([0-9]{5})(-[0-9]{4})?$/i",$zip_code))
    return true;
  else
    return false;
}
function isValidPSTNumber($pst_number)
{
  // This matches only BC PST numbers.  Before using this, code it to handle PST of other provinces.
  if (!preg_match("/^[0-9]{4}-[0-9]{4}$/", trim($pst_number))) 
    return false;
  else
    return true;
}
function isValidAccount($account_name)
{
  $query = "SELECT ID from signboom_user WHERE AcctName = '$account_name'";
  $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
  $num_rows = mysqli_num_rows($result);
  if ($num_rows > 0)
    return true;
  else
    return false;
}
function isPrintableProduct($product_code)
{
  $query = "SELECT Category from signboom_allproducts WHERE Code = '$product_code'";
  $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
  $num_rows = mysqli_num_rows($result);
  if ($num_rows == 0)
    return false;

  $row = mysqli_fetch_array($result,  MYSQLI_BOTH);
  $category = $row['Category'];
  if (($category != 'STANDS') && ($category != 'ACCESS'))
    return true;
  else
    return false;
}
function isValidDiscountCode($discount_code)
{
  $query = "SELECT ID from signboom_discount WHERE ID = '$discount_code'";
  $result = mysqli_query($GLOBALS["___mysqli_ston"], $query);
  $num_rows = mysqli_num_rows($result);
  if ($num_rows > 0)
    return true;
  else
    return false;
}
function isValidDate($date)
{
  if (preg_match("/^(\d{4})-(\d{2})-(\d{2})$/", $date, $matches)) {
    if (checkdate($matches[2], $matches[3], $matches[1])) {
      return true;
    }
  }
  return false;
} 
function isValidTeam($team)
{
  if ((strcmp($team, "ONLINE") == 0) ||
      (strcmp($team, "OFFLINE") == 0))
    return true;
  else
    return false;
}
?>

