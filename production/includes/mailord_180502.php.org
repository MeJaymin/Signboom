<?php
# 
#     SAMPLE ARRAY {{{
# 
# $message[1]['content_type'] = 'text/plain; charset=us-ascii';
# $message[1]['filename'] = '';
# $message[1]['no_base64'] = TRUE;
# $message[1]['data'] = "Hi, how are you doing?\n  sincerely, Me";
# 
# $message[2]['content_type'] = 'text/plain';
# $message[2]['filename'] = '.vimrc';
# $message[2]['data'] = mp_read_file('/home/nobody/.vimrc');
# $message[2]['headers'] = array('X-Sent-By' => 'YourName@planet.mars', 'X-mailer' => 'Fish egg soup 1.0');
# 
# $message[3]['content_type'] = 'image/jpeg';
# $message[3]['filename'] = 'latest.jpg';
# $message[3]['data'] = mp_read_file('/home/nobody/latest.jpg');
# $message[3]['headers'] = array('X-Sent-By' => 'YourName@planet.mars', 'X-mailer' => 'Pine 3.31');
#
# }}}
# 
#     COMPLETE SAMPLE {{{
# 
# 1) Use $message from above.
# 2) The php mail function
# 
# $out = mp_new_message($message);
# mail('to_whom_it_may_concern@planet.mars', 'Your subject', 
#      $out[0], "From: from_who_it_concerned@planet.venus\n".$out[1]);
# 
# }}}
#
# # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # # 

# DEFINES {{{
if (strlen(getenv('HOSTNAME')))
  define('HOSTNAME', getenv('HOSTNAME'));
else
  define('HOSTNAME', 'nohost.nodomain');

# }}}

# public functions
function mp_read_file($filename)/*{{{*/
{
  $buf = '';
  $fd = fopen($filename, 'r');
  if ($fd)
  {
    while(!feof($fd))
    {
      $buf .= fread($fd, 256);
    }
    fclose($fd);
  }
  if (strlen($buf))
    return $buf;
}/*}}}*/

function mp_new_message(&$message_array)/*{{{*/
{
  $boundary = mp_new_boundary();
  while(list(, $chunk) = each($message_array))
  {
    $mess = TRUE;
    unset($headers);
    unset($data);
    if (!$chunk['no_base64'])
    {
      $headers['Content-ID'] = mp_new_message_id();
      $headers['Content-Transfer-Encoding'] = 'BASE64';
      if (strlen($chunk['filename']))
      {
        $headers['Content-Type'] = $chunk['content_type'].'; name="'.$chunk['filename'].'"';
        $headers['Content-Description'] = '';
        $headers['Content-Disposition'] = 'attachment; filename="'.$chunk['filename'].'"';
      }
      else
      {
        $headers['Content-Type'] = $chunk['content_type'];
      }
      $data = chunk_split(base64_encode($chunk['data']),60,"\n");
    }
    else
    {
      $headers['Content-Type'] = $chunk['content_type'];
      $data = $chunk['data'] . "\n";
    }

    if (is_array($chunk['headers']) && count($chunk['headers']))
    {
      while(list($key, $val) = each($chunk['headers']))
      {
        $headers[$key] = $val;
      }
    }

    $buf .= '--' . $boundary. "\n";
    while(list($key, $val) = each($headers))
    {
      $buf .= $key.': '.$val."\n";
    }
    $buf .= "\n";
    $buf .= $data;

  }

    if ($mess)
    {
      $buf .= '--' . $boundary. '--' ;   

        return array 
        (
          0 => $buf,
          1 => 'MIME-Version: 1.0'."\n".
          'Content-Type: MULTIPART/MIXED;'."\n".
            '  BOUNDARY="'.$boundary.'"'."\n".
          'X-Generated-By: ajm Software;'."\n".
            '  http://www.ajmsoft.com/',
          2 => array('MIME-Version: 1.0', 
                'Content-Type: MULTIPART/MIXED;'."\n"
                  .'  BOUNDARY="'.$boundary.'"',
                'X-Generated-By: ajm Software;'."\n"
                  .'  http://www.ajmsoft.com/')
        );

    }
  }/*}}}*/
  
  # private functions
  function mp_new_message_id()/*{{{*/
  {
    return '<'.'lib_multipart-'.str_replace(' ','.',microtime()).'@'.HOSTNAME.'>';
  }/*}}}*/

  function mp_new_boundary()/*{{{*/
  {
      return '-'.'lib_multipart-'.str_replace(' ','.',microtime());
  }/*}}}*/



  function bldhtml($rsmaster, $dtl, $rsUser, $intro) {
    if ($intro == "") {
      $intro  = "You will receive another confirmation once ";
      $intro .= "your file(s) have been reviewed and are queued for printing.<br><br>";
      $intro .= "Once they are printed and inspected, you will receive an additional ";
      $intro .= "email letting you know they are ready to ship or pick up per ";
      $intro .= "your original instructions.<br><br>";
    }

    $msg = "";

    $msg = "<html>\n";
    $msg .= "<head>\n";
    $msg .= '<title>Signboom Confirmation</title>';
    $msg .= '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">';
    $msg .= '</head>';
    $msg .= '<body>';
    $msg .= '<img src="http://www.signboom.com/images/logo3d.gif" width="308" height="54">';
    $msg .= '<br><br>';
    $msg .= '<table border="0" width="600" cellspacing="0" cellpadding="4" style="font-family: Arial; font-size: 12px;">';
    $msg .= '  <tr>';
    $msg .= '    <td colspan="2">Thank you for your order.</td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td colspan="2">';
    $msg .=        $intro;
    $msg .= '    </td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td>Name:</td>';
    $msg .= '    <td>&nbsp;'.mysql_result($rsUser,0,'firstName')." ".mysql_result($rsUser,0,'lastName').'</td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td>Company:</td>';
    $msg .= '    <td>&nbsp;'.mysql_result($rsUser,0,'company').'</td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td>Phone:</td>';
    $msg .= '    <td>&nbsp;'.mysql_result($rsUser,0,'phone1').' OR '.mysql_result($rsUser,0,'phone2'). '</td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td>Account:</td>';
    $msg .= '    <td>&nbsp;'.mysql_result($rsUser,0,'AcctName').'</td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td>Ship To:</td>';
    $msg .= '    <td>&nbsp;'.mysql_result($rsmaster,0,'shipcompany').'</td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td>&nbsp;</td>';
    $msg .= '    <td>&nbsp;'.mysql_result($rsmaster,0,'shipaddress').'</td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td>&nbsp;</td>';
    $msg .= '    <td>&nbsp;'.mysql_result($rsmaster,0,'shipcity').', '.mysql_result($rsmaster,0,'shipprov').' '.mysql_result($rsmaster,0,'shipzip').'</td>';
    $msg .= '  </tr>';

    if (strlen(trim(mysql_result($rsmaster,0,'documentname'))) > 0)
    {
      $msg .= '  <tr>';
      $msg .= '    <td>Shipping Label:</td>';
      $msg .= '    <td style="color: #cc0000;">Shipping label has been provided with order.</td>';
      $msg .= '  </tr>';
    }

    $msg .= '  <tr>';
    $msg .= '    <td>Ready Date:</td>';
    $msg .= '    <td>'.mysql_result($rsmaster,0,'readydate').'</td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td>Order Number:</td>';
    $msg .= '    <td>'.mysql_result($rsmaster,0,'ID').'&nbsp;</td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td>Reference Number:</td>';
    $msg .= '    <td>'.mysql_result($rsmaster,0,'refnum').'&nbsp;</td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td>Customer Notes:</td>';
    $msg .= '    <td>'.mysql_result($rsmaster,0,'customernotes').'&nbsp;</td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '  <tr>';
    $msg .= '  <td colspan="2"><br><br><table border="0" cellspacing="0" cellpadding="4" style="font-family: Arial; font-size: 12px;">';
    $msg .= '    <tr>';
    $msg .= '    <td><b>Product</b></td>';
    $msg .= '    <td style="width: 300px;"><b>Options</b></td>';
    $msg .= '    <td><b>Qty</b></td>';
    $msg .= '    <td><b>Size</b></td>';
    $msg .= '    <td><b>File Name</b></td>';
    $msg .= '    </tr>';

    $order_cost = 0.0;
    // For each line in the order...
    for ($i = 1; $i <= 10; $i++) { 

      // If the line isn't empty...
      if ($dtl[$i]->code != "") {

        $msg .= '  <tr>';
        // Product (media) goes here.
        $msg .= '    <td>'.$dtl[$i]->code.'</td>';

        // Options go here.
        $msg .= '    <td>&nbsp;'.$dtl[$i]->options.'</td>';

        // Quantity here
        $msg .= '    <td>'.$dtl[$i]->quantity.'</td>';

        // Size = width x height goes here
        $msg .= '    <td>'.$dtl[$i]->width."x".$dtl[$i]->height.'</td>';

        // Keep track of total pre-discount cost of line items.
        $order_cost += substr($dtl[$i]->total, 1);

        // Filename goes here
        $msg .= '    <td>'.stripname($dtl[$i]->filename).'</td>';
        $msg .= '  </tr>';

        // If there is a second filename, put it on line below.
        if (stripname($dtl[$i]->filename2) != "") {
          $msg .= '  <tr>';
          $msg .= '    <td colspan="4">&nbsp;</td>';
          $msg .= '    <td>'.stripname($dtl[$i]->filename2).'</td>';
          $msg .= '  </tr>';
        }
      }
    }
    $msg .= '    </table></td>';
    $msg .= '  </tr>';

    // Calculate the percentage discount which has been applied to the order. 
    // We no longer grab the discount percentage from the user database, because that
    // discount is only applied to the DISCOUNTABLE amount of the media costs.
    // Instead, the javascript code calculates a dollar amount for the discount, choosing
    // either the customer's discount level, or an order-size based email, whichever is
    // a better deal for the customer. Then we convert that to a percentage (here) and 
    // then (below) apply that percentage evenly to each row in the billing summary.
    $discount_dollars = mysql_result($rsmaster,0,'discount');
    $discount_amount = substr($discount_dollars, 1);
    $discount_percentage = $discount_amount / $order_cost;


    //-------------- Start of Billing Summary Table --------------
    $msg .= '  <td colspan="2"><table border="0" cellspacing="0" cellpadding="4" style="font-family: Arial; font-size: 12px;">';
    $msg .= '    <tr>';
    $msg .= '      <td colspan="4"><br><br><b>Billing Summary</b><br><hr></td>';
    $msg .= '    </tr>';

    // First, total up the amount of material used in each medium.
    $linecode = "";
    for ($i = 1; $i <= 10; $i++) { 
      if ($linecode != $dtl[$i]->code) {
        if ($linecode != "") {
          // This is a new medium.  Print out results for previous medium.
          $msg .= '  <tr>';
          $msg .= '    <td>'.$linecode.'</td>';
          if ($linesfootage == 0.0) $msg .= '    <td></td>';
          else                      $msg .= '    <td>'.sprintf("%01.3f", $linesfootage).' sq ft</td>';
          $msg .= '    <td></td>'; // where $linelfootage used to go
          // Apply the percentage discount evenly across all items.
          $cost_net_discount = $lineamt * (1.0 - $discount_percentage);
          $msg .= '    <td style="text-align: right;">'.getcurrency($cost_net_discount).'&nbsp;</td>';
          $msg .= '  </tr>';
        }
        $linecode = $dtl[$i]->code;
        $linesfootage = 0;
        $lineamt = 0;
      }

      if ($dtl[$i]->code != "") {
        $lineamt += (substr($dtl[$i]->total, 1));
        $wsfootage = trim($dtl[$i]->sqfootage);
        $linesfootage += $wsfootage;
      }

    }

    // Print out results for final medium, if there is one.
    if ($linecode != "") {
      $msg .= '  <tr>';
      $msg .= '    <td>'.$linecode.'</td>';
      if ($linesfootage == 0.0) $msg .= '    <td></td>';
      else                      $msg .= '    <td>'.sprintf("%01.3f", $linesfootage).' sq ft</td>';
      $msg .= '    <td></td>'; // where $linealfootage used to go
      // Apply the percentage discount evenly across all items.
      $cost_net_discount = $lineamt * (1.0 - $discount_percentage);
      $msg .= '    <td style="text-align: right;">'.getcurrency($cost_net_discount).'&nbsp;</td>';
      $msg .= '  </tr>';
    }

    $msg .= '    <tr>';
    $msg .= '      <td colspan="3">Setup Fee: </td>';
    $msg .= '      <td style="text-align: right;">'.getcurrency(substr(mysql_result($rsmaster,0,'setupfee'), 1)).'&nbsp;</td>';
    $msg .= '    </tr>';
    $msg .= '    <tr>';

    if (mysql_result($rsmaster,0,'promodiscount') != "0.00" ) {
          $msg .= '  <tr>';
          $msg .= '    <td>Promo Discount: </td>';
          $msg .= '      <td>&nbsp;'.mysql_result($rsmaster,0,'promocode').'</td>';
          $msg .= '      <td>&nbsp;</td>';
          $msg .= '    <td style="text-align: right;">-$'.mysql_result($rsmaster,0,'promodiscount').'&nbsp;</td>';
          $msg .= '  </tr>';
    }

    if (mysql_result($rsmaster,0,'rushfee') != "$0.00" ) {
      $msg .= '  <tr>';
      $msg .= '    <td colspan="3">Rush Charges: </td>';
      $msg .= '    <td style="text-align: right;">'.mysql_result($rsmaster,0,'rushfee').'&nbsp;</td>';
      $msg .= '  </tr>';
    }
    $msg .= '      <td>Freight: </td>';
    $msg .= '      <td>&nbsp;'.mysql_result($rsmaster,0,'shiptype').'</td>';
    $msg .= '      <td>&nbsp;</td>';
    $msg .= '      <td style="text-align: right;">'.mysql_result($rsmaster,0,'freight').'&nbsp;</td>';
    $msg .= '    </tr>';
    if (mysql_result($rsmaster,0,'pst') != "$0.00" ) {
          $msg .= '  <tr>';
          $msg .= '    <td colspan="3">PST: </td>';
          $msg .= '    <td style="text-align: right;">'.mysql_result($rsmaster,0,'pst').'&nbsp;</td>';
          $msg .= '  </tr>';
    }
    if (mysql_result($rsmaster,0,'gst') != "$0.00" ) {
          $msg .= '  <tr>';
          $msg .= '    <td colspan="3">GST: </td>';
          $msg .= '    <td style="text-align: right;">'.mysql_result($rsmaster,0,'gst').'&nbsp;</td>';
          $msg .= '  </tr>';
    }
    $msg .= '  <tr>';
    $totchg = substr(trim(mysql_result($rsmaster,0,'ordertotal')),1);
    $totchg = ($totchg == 0) ? "Call for Quote" : "$".sprintf("%01.2f", $totchg);
    $msg .= '    <td colspan="3">Total: </td>';
    $msg .= '    <td style="text-align: right;">'.mysql_result($rsmaster,0,'ordertotal').'&nbsp;</td>';
    $msg .= '  </tr>';
    $msg .= '  </table>';
    //*************** End of Billing Summary Table *********************/

    $msg .= '  </td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td colspan="2"><br><br><a href="http://www.signboom.com">www.signboom.com</a></td>';
    $msg .= '  </tr>';
    $msg .= '  <tr>';
    $msg .= '    <td colspan="2">'.date("D M j G:i:s T Y").'</td>';
    $msg .= '  </tr>';
    $msg .= '</table>';
    $msg .= '</body>';
    $msg .= '</html>';
    return $msg;
  }

  function GetProduct($id) {
    global $database_DBConn, $DBConn;
    if ($id == 0) return "None";
    $qry = "SELECT ProductType FROM signboom_product WHERE ID = ".$id;
    $rs = mysql_query($qry, $DBConn) or die(mysql_error());
    if (mysql_num_rows($rs) == 0) return "Not Available";
    $msg = mysql_result($rs,0,'ProductType');
    return $msg;
  }

  function GetFeature($id) {
    global $database_DBConn, $DBConn;
    if ($id == 0) return "None";
    $qry = "SELECT * FROM signboom_feature WHERE ID = ".$id;
    $rs = mysql_query($qry, $DBConn) or die(mysql_error());
    if (mysql_num_rows($rs) == 0) return "Not Available";
    $msg = mysql_result($rs,0,'Desc');
    return $msg;
  } 

  function GetDiscount($id) {
    global $database_DBConn, $DBConn;
    $qry = "SELECT * FROM signboom_discount WHERE Enabled = 1 AND ID = '".$id."'";
    $rs = mysql_query($qry, $DBConn) or die(mysql_error());
    if (mysql_num_rows($rs) == 0) return 0;
    return mysql_result($rs,0,'Dct');
  } 

  function stripname($f) {
    $z = strrchr($f, "\\");
    if (!($z)) {
      return $f;
    } else {
      return substr($z, 1); 
    }
  }

  function dispdate($d) {
    $dd = date("Ymd", strtotime($d));
    return ($dd == "20000101") ? "Call" : date("D M j Y", strtotime($d));
  }
  function getcurrency($num) {
    //print sprintf('$'."%01.0f", $num);
    if (is_numeric($num)) {
      return sprintf('$'."%01.2f", $num);
    } else {
      return $num;
    }
  }

?>
