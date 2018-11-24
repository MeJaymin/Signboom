<?php 
require('authprodn.php');

$query_offcuts = "SELECT * FROM signboom_offcuts WHERE Used = 0 ORDER BY Material ASC, Length ASC, DateAdded ASC";
$offcuts = mysql_query($query_offcuts, $DBConn) or die("Offcuts: Could not read offcuts from database:" . mysql_error() . "<br><br>" . $query_offcuts);
$number_of_offcuts = mysql_num_rows($offcuts);

// Handle any ticks user has made in checkboxes.
include('includes/javascript_clicked_offcuts.htm');
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>

<head>

  <title>Offcuts</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <link href="production-new.css" rel="stylesheet" type="text/css">
</head>

<body>

<div style="width: 1400px; margin: 0px auto; text-align: center;">

  <div style="float: left; margin-top: 20px;"><img src="../../images/logo3d.gif" width="308" height="54"></div>
  <div style="float: right;"><h1>Order Processing System: Offcuts</h1></div>
  <?php include('menu.html');?>
  <br><br>

  <div style="width: 900px; margin: 0px auto; text-align: center;">
    <b><a href="select-offcut.php">EDIT OFFCUT</a>&nbsp;&nbsp;&nbsp;<a href="create-offcut.php">NEW OFFCUT</a></b>
    <br><br>
  </div>

  <!-- Form which passes tickbox ticks back into PHP to be handled. -->
  <form name="offcuts_form" action=<?php echo $_SERVER['PHP_SELF'] ?> method="POST">
    <input name="number_of_offcuts" id="number_of_offcuts" type="hidden" value="<?php echo $number_of_offcuts; ?>">

    <table class="evenodd" border="0" align="center" cellpadding="5">
      <tr class="heading">
        <td class="heading">Offcut ID</td>
        <td class="heading date">Date Added</td>
        <td class="heading">Added By</td>
        <td class="heading">Material</td>
        <td class="heading">Length</td>
        <td class="heading">Width</td>
        <td class="heading">Quantity</td>
        <td class="heading date">Date Claimed</td>
        <td class="heading">Claimed By</td>
        <td class="heading date">Date Used</td>
        <td class="heading">Person Used</td>
        <td class="heading">Paid For</td>
        <td class="heading">Description</td>
        <td class="heading">Claimed</td>
        <td class="heading">Used</td>
      </tr>
      <tr>
        <td colspan="15" style="background-color: #ffffff;"><hr></td>
      </tr>

    <?php 
      include('includes/handle_offcut_submit.php');

      // Display the offcuts.
      for ($i = 0; $i < $number_of_offcuts; $i++) 
      {
        $row = mysql_fetch_assoc($offcuts);
        if ($row == FALSE)
	{
          echo "Could not read offcut information from database.";
	}
        else
	{
          $offcut_id = $row['OffcutId'];
          $date_added = $row['DateAdded'];
          $person_added = $row['PersonAdded'];
          $material = $row['Material'];
          $width = $row['Width'];
          $length = $row['Length'];
          $quantity = $row['Quantity'];
          $claimed = $row['Claimed'];
          $date_claimed = $row['DateClaimed'];
          $person_claimed = $row['PersonClaimed'];
          $used = $row['Used'];
          $date_used = $row['DateUsed'];
          $person_used = $row['PersonUsed'];
          $paid_for = $row['PaidFor'];
          $description = $row['Description'];

	  echo "<tr>";
          echo "<td>$offcut_id <input name=\"offcut_id_$i\" id=\"offcut_id_$i\" type=\"hidden\" value=\"$offcut_id\"></td>";
          echo "<td>$date_added</td>";
          echo "<td>$person_added</td>";
          echo "<td>$material</td>";
          echo "<td>$length</td>";
          echo "<td>$width</td>";
          echo "<td>$quantity</td>";
          echo "<td>$date_claimed</td>";
          echo "<td>$person_claimed</td>";
          echo "<td>$date_used</td>";
          echo "<td>$person_used</td>";
	  if ($paid_for)
            echo "<td>Yes</td>";
          else
            echo "<td>No</td>";

          if (strlen($description) > 20) {
             echo "<td><a href=\"#\" onClick=\"alert('$description')\">";
             echo substr($description, 0, 16); 
             echo "...";
             echo "</a></td>";
          }
          else {
            echo "<td>$description</td>";
          }

          echo '<td style="text-align: center;">';
          if ($used == 1) 
            echo "<input name=\"claimed_$i\" type=\"checkbox\" id=\"claimed_$i\" value=\"1\" class=\"myinput\" CHECKED DISABLED>";
          else if ($claimed == 1) 
            echo "<input name=\"claimed_$i\" type=\"checkbox\" id=\"claimed_$i\" value=\"1\" class=\"myinput\" CHECKED onClick=\"CheckboxTicked($i, 'claimed', $offcut_id)\">";
          else 
            echo "<input name=\"claimed_$i\" type=\"checkbox\" id=\"claimed_$i\" value=\"1\" class=\"myinput\" onClick=\"CheckboxTicked($i, 'claimed', $offcut_id)\">";
          echo "</td>";

          echo '<td style="text-align: center;">';
          if ($used == 1) 
            echo "<input name=\"used_$i\" type=\"checkbox\" id=\"used_$i\" value=\"1\" class=\"myinput\" CHECKED DISABLED>";
          else 
            echo "<input name=\"used_$i\" type=\"checkbox\" id=\"used_$i\" value=\"1\" class=\"myinput\" onClick=\"CheckboxTicked($i, 'used', $offcut_id)\">";
          echo "</td>";

	  echo "</tr>";
        } 
      }
    ?>

      <tr>
        <td colspan="15"><hr></td>
      </tr>
      <tr style="background-color: #ffffff;">
        <td colspan="12" style="background-color: #ffffff;"></td>
        <td colspan="3" style="background-color: #ffffff;"><input type="submit" name="update_offcuts" value="Update Offcut Statuses" onclick="document.offcuts_form.submit();"></td>
      </tr>

    </table>

  </form> 

</div>
</body>
</html>
<?php
mysql_free_result($jobs);
?>

