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

    <h1>Summary of Media Ordered</h1>

    <table border="0" align="center" cellpadding="5">
      <tr>
        <td class="heading">Product<br></td>
        <td class="heading" style="text-align:right;">Square Footage<br></td>
        <td class="heading" style="text-align:right;">Product Cost<br></td>
      </tr>

      <tr>
        <td colspan="3"><hr></td>
      </tr>

    <?php
    for ($k = 0; $k < count($product_information); $k++):
    ?>

      <tr>
        <td class="lineitem_std"><?php echo $product_information[$k]['product']; ?></td>
        <td class="lineitem_std" style="text-align: right;"><?php echo $product_information[$k]['squarefootage']; ?></td>
        <td class="lineitem_std" style="text-align: right;"><?php echo $product_information[$k]['cost']; ?></td>
      </tr>

    <?php
    endfor;
    ?>

      <tr>
        <td colspan="3"><hr></td>
      </tr>

      <tr>
        <td colspan="2" class="lineitem_std" style="text-align: right; padding-right: 20px;">
          Total Cost for Products: <br><br>
        </td>
        <td class="lineitem_std" style="text-align: right; padding-right: 10px;">
          <?php printf("&#036;%.2f<br><br>", $total_cost); ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="lineitem_std" style="text-align: right; padding-right: 20px;">
          Rush/Hot Fees from PDW Order Page: <br><br>
        </td>
        <td class="lineitem_std" style="text-align: right;">
          <?php printf("&#036;%.2f<br><br>", $total_rush_fees); ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="lineitem_std" style="text-align: right; padding-right: 20px;">
          Rush/Hot Fees from Signboom Order Page: <br><br>
        </td>
        <td class="lineitem_std" style="text-align: right;">
          <?php printf("&#036;%.2f<br><br>", $order_rush_fees); ?>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="lineitem_std" style="text-align: right; padding-right: 20px;">
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
