<!doctype html>

<html>

<head>
  <meta charset="utf-8">
  <title>Signboom: PWD Order System</title>
  <?php include ('head.html'); ?>
  <link rel="stylesheet" type="text/css" media="all" href="styles.css" />
</head>

<body>

<div id="page_wide">

  <?php include ('banner-menu.html'); ?>

  <div id="content_wide">

   <h1>Details of Items Ordered</h1>

    <table border="0" align="center" cellpadding="5">
      <tr>
        <td class="heading">Order Page<br></td>
        <td class="heading">Order ID<br></td>
        <td class="heading">File ID<br></td>
        <td class="heading">Product<br></td>
        <td class="heading">Quantity<br></td>
        <td class="heading">Double Sided<br></td>
        <td class="heading">Ready Date<br></td>
        <td class="heading" style="text-align:right;">Product Cost<br></td>
        <td class="heading">Rush Type<br></td>
        <td class="heading" style="text-align:right;">Rush Fees<br></td>
        <td class="heading" style="width: 170px;">File Name<br></td>
      </tr>

      <tr>
        <td colspan="11"><hr></td>
      </tr>

    <?php
    for ($k = 0; $k < $num_jobs; $k++):
    ?>

      <tr>
        <td class="lineitem_std"><?php echo $job_array[$k]['ordertype']; ?></td>
        <td class="lineitem_std"><?php echo $job_array[$k]['orderid']; ?></td>
        <td class="lineitem_std"><?php echo $job_array[$k]['jobid']; ?></td>
        <td class="lineitem_std"><?php echo $job_array[$k]['product']; ?></td>
        <td class="lineitem_std"><?php echo $job_array[$k]['quantity']; ?></td>
        <td class="lineitem_std"><?php echo $job_array[$k]['double_sided']; ?></td>
        <td class="lineitem_std"><?php echo $job_array[$k]['readydate']; ?></td>
        <td class="lineitem_std" style="text-align: right;">$<?php echo $job_array[$k]['cost']; ?></td>
        <td class="lineitem_std"><?php echo $job_array[$k]['rushtype']; ?></td>
        <td class="lineitem_std" style="text-align: right;">$<?php echo $job_array[$k]['rushcost']; ?></td>
        <td class="lineitem_std" style="text-align: left;"><?php echo $job_array[$k]['filename']; ?></td>
      </tr>

    <?php
    endfor;
    ?>

      <tr>
        <td colspan="11"><hr></td>
      </tr>

      <tr>
        <td colspan="10" class="lineitem_std" style="text-align: right; padding-right: 20px;">
          Total Cost for Products: <br><br>
        </td>
        <td class="lineitem_std" style="text-align: right; padding-right: 10px;">
          <?php printf("&#036;%.2f<br><br>", $total_product_cost); ?>
        </td>
      </tr>
      <tr>
        <td colspan="10" class="lineitem_std" style="text-align: right; padding-right: 20px;">
          Rush/Hot Fees from PDW Order Page: <br><br>
        </td>
        <td class="lineitem_std" style="text-align: right;">
          <?php printf("&#036;%.2f<br><br>", $total_rush_fees); ?>
        </td>
      </tr>
      <tr>
        <td colspan="10" class="lineitem_std" style="text-align: right; padding-right: 20px;">
          Rush/Hot Fees from Signboom Order Page: <br><br>
        </td>
        <td class="lineitem_std" style="text-align: right;">
          <?php printf("&#036;%.2f<br><br>", $order_rush_fees); ?>
        </td>
      </tr>
      <tr>
        <td colspan="10" class="lineitem_std" style="text-align: right; padding-right: 20px;">
          Total not including Taxes and Cost of Stands (easels however are included): <br><br>
        </td>
        <td class="lineitem_std" style="text-align: right;">
          <?php printf("&#036;%.2f<br><br>", $total_total); ?>
        </td>
      </tr>

    </table>

  </div>

</div>

</body>
