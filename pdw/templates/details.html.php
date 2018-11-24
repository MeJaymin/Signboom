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

   <h1><?php echo $page_title; ?></h1>

    <table border="0" align="center" cellpadding="5">
      <tr>
        <td class="heading">File ID<br></td>
        <td class="heading">Product<br></td>
        <td class="heading">Quantity<br></td>
        <td class="heading">Height<br></td>
        <td class="heading">Width<br></td>
        <td class="heading">Double Sided<br></td>
        <td class="heading">Square Footage<br></td>
        <td class="heading">Finishing<br></td>
        <td class="heading" style="text-align:right;">Unit Cost<br></td>
        <td class="heading" style="text-align:right;">Total Cost<br></td>
        <td class="heading" style="width: 170px;">File Name<br></td>
      </tr>

      <tr>
        <td colspan="11"><hr></td>
      </tr>

    <?php
    for ($k = 0; $k < $num_jobs; $k++):
    ?>

      <tr>
        <td class="lineitem_std"><?php echo $job_array[$k]['jobid']; ?></td>
        <td class="lineitem_std"><?php echo $job_array[$k]['product']; ?></td>
        <td class="lineitem_std" style="text-align: right;"><?php echo $job_array[$k]['quantity']; ?></td>
        <td class="lineitem_std" style="text-align: right;"><?php echo $job_array[$k]['height']; ?></td>
        <td class="lineitem_std" style="text-align: right;"><?php echo $job_array[$k]['width']; ?></td>
        <td class="lineitem_std"><?php echo $job_array[$k]['double_sided']; ?></td>
        <td class="lineitem_std" style="text-align: right;"><?php echo $job_array[$k]['square_footage']; ?></td>
        <td class="lineitem_std"><?php echo $job_array[$k]['finishing_option_set']; ?></td>
        <td class="lineitem_std" style="text-align: right;">$<?php echo $job_array[$k]['unit_cost']; ?></td>
        <td class="lineitem_std" style="text-align: right;">$<?php echo $job_array[$k]['cost']; ?></td>
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

    </table>

  </div>

</div>

</body>
