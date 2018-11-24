<!doctype html>

<html>

<head>
  <meta charset="utf-8">
  <title>Signboom: PWD Order System</title>
  <?php include ('head.html'); ?>
  <link rel="stylesheet" type="text/css" media="all" href="styles.css" />
</head>

<body>

<div id="page">

  <?php include ('banner-menu.html'); ?>

  <div id="content">

    <h1>Dashboard - Main Page</h1>

    <table class="withborder" style="margin: 10px auto;" cellpadding="5">
    <tr>
      <td class="table_title">Your Order</td>
      <td class="heading_grey">Upload in</td>
      <td class="heading">Being</td>
      <td class="heading_grey">Queued for</td>
      <td class="heading">Being</td>
      <td class="heading_grey">Items</td>
      <td class="heading">All Items</td>
    </tr>
    <tr>
      <td class="table_title">Summary</td>
      <td class="heading_grey">Progress</td>
      <td class="heading">Proofed</td>
      <td class="heading_grey">Printing</td>
      <td class="heading">Finished</td>
      <td class="heading_grey">Completed</td>
      <td class="heading">Ordered</td>
    </tr>
    <tr>
      <td colspan="12"><hr></td>
    </tr>
    <tr>
      <td class="heading">Total</td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_total&end=$end_total&page_description=All Orders&special_case=TOTAL&stage=UPLOADING\"> $files_uploading_total</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_total&end=$end_total&page_description=All Orders&special_case=TOTAL&stage=PREFLIGHT\">$files_preflight_total</a>"; ?></td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_total&end=$end_total&page_description=All Orders&special_case=TOTAL&stage=QUEUED\"> $files_queued_total</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_total&end=$end_total&page_description=All Orders&special_case=TOTAL&stage=PRINTED\">$files_finishing_total</a>"; ?></td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_total&end=$end_total&page_description=All Orders&special_case=TOTAL&stage=COMPLETE\">$files_complete_total</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_total&end=$end_total&page_description=All Orders&special_case=TOTAL&stage=TOTAL BACKLOG\">$files_total_total</a>"; ?></td>
    </tr>
    <tr>
      <td class="heading">Hots</td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_total&end=$end_total&page_description=HOTS&special_case=HOT&stage=UPLOADING\">$files_uploading_hots</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_total&end=$end_total&page_description=HOTS&special_case=HOT&stage=PREFLIGHT\">$files_preflight_hots</a>"; ?></td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_total&end=$end_total&page_description=HOTS&special_case=HOT&stage=QUEUED\"> $files_queued_hots</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_total&end=$end_total&page_description=HOTS&special_case=HOT&stage=PRINTED\">$files_finishing_hots</a>"; ?></td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_total&end=$end_total&page_description=HOTS&special_case=HOT&stage=COMPLETE\">$files_complete_hots</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_total&end=$end_total&page_description=HOTS&special_case=HOT&stage=TOTAL BACKLOG\">$files_total_hots</a>"; ?></td>
    </tr>
    <tr>
      <td class="heading">Late</td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_late&end=$end_late&page_description=LATE&stage=UPLOADING\">$files_uploading_late</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_late&end=$end_late&page_description=LATE&stage=PREFLIGHT\">$files_preflight_late</a>"; ?></td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_late&end=$end_late&page_description=LATE&stage=QUEUED\">$files_queued_late</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_late&end=$end_late&page_description=LATE&stage=PRINTED\">$files_finishing_late</a>"; ?></td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_late&end=$end_late&page_description=LATE&stage=COMPLETE\">$files_complete_late</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_late&end=$end_late&page_description=LATE&stage=TOTAL BACKLOG\">$files_total_late</a>"; ?></td>
    </tr>
    <tr>
      <td class="heading">Due Today</td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_today&end=$end_today&page_description=DUE TODAY&stage=UPLOADING\">$files_uploading_today</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_today&end=$end_today&page_description=DUE TODAY&stage=PREFLIGHT\">$files_preflight_today</a>"; ?></td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_today&end=$end_today&page_description=DUE TODAY&stage=QUEUED\">$files_queued_today</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_today&end=$end_today&page_description=DUE TODAY&stage=PRINTED\">$files_finishing_today</a>"; ?></td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_today&end=$end_today&page_description=DUE TODAY&stage=COMPLETE\">$files_complete_today</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_today&end=$end_today&page_description=DUE TODAY&stage=TOTAL BACKLOG\">$files_total_today</a>"; ?></td>
    </tr>
    <tr>
      <td class="heading">Due this Week</td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_this_week&end=$end_this_week&page_description=DUE THIS WEEK&stage=UPLOADING\">$files_uploading_this_week</a>"; ?></td>

      <td><?php echo "<a href=\"jobs.php?start=$start_this_week&end=$end_this_week&page_description=DUE THIS WEEK&stage=PREFLIGHT\">$files_preflight_this_week</a>"; ?></td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_this_week&end=$end_this_week&page_description=DUE THIS WEEK&stage=QUEUED\">$files_queued_this_week</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_this_week&end=$end_this_week&page_description=DUE THIS WEEK&stage=PRINTED\">$files_finishing_this_week</a>"; ?></td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_this_week&end=$end_this_week&page_description=DUE THIS WEEK&stage=COMPLETE\">$files_complete_this_week</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_this_week&end=$end_this_week&page_description=DUE THIS WEEK&stage=TOTAL BACKLOG\">$files_total_this_week</a>"; ?></td>
    </tr>
  <tr>
    <td class="heading">Due Next Week</td>
    <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_next_week&end=$end_next_week&page_description=DUE NEXT WEEK&stage=UPLOADING\">$files_uploading_next_week</a>"; ?></td>

    <td><?php echo "<a href=\"jobs.php?start=$start_next_week&end=$end_next_week&page_description=DUE NEXT WEEK&stage=PREFLIGHT\">$files_preflight_next_week</a>"; ?></td>
    <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_next_week&end=$end_next_week&page_description=DUE NEXT WEEK&stage=QUEUED\">$files_queued_next_week</a>"; ?></td>
    <td><?php echo "<a href=\"jobs.php?start=$start_next_week&end=$end_next_week&page_description=DUE NEXT WEEK&stage=PRINTED\">$files_finishing_next_week</a>"; ?></td>
    <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_next_week&end=$end_next_week&page_description=DUE NEXT WEEK&stage=COMPLETE\">$files_complete_next_week</a>"; ?></td>
    <td><?php echo "<a href=\"jobs.php?start=$start_next_week&end=$end_next_week&page_description=DUE NEXT WEEK&stage=TOTAL BACKLOG\">$files_total_next_week</a>"; ?></td>
    </tr>
    <tr>
      <td class="heading">Due Later</td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_later&end=$end_later&page_description=DUE LATER&stage=UPLOADING\">$files_uploading_later</a>"; ?></td>

      <td><?php echo "<a href=\"jobs.php?start=$start_later&end=$end_later&page_description=DUE LATER&stage=PREFLIGHT\">$files_preflight_later</a>"; ?></td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_later&end=$end_later&page_description=DUE LATER&stage=QUEUED\">$files_queued_later</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_later&end=$end_later&page_description=DUE LATER&stage=PRINTED\">$files_finishing_later</a>"; ?></td>
      <td class="grey_col"><?php echo "<a href=\"jobs.php?start=$start_later&end=$end_later&page_description=DUE LATER&stage=COMPLETE\">$files_complete_later</a>"; ?></td>
      <td><?php echo "<a href=\"jobs.php?start=$start_later&end=$end_later&page_description=DUE LATER&stage=TOTAL BACKLOG\">$files_total_later</a>"; ?></td>
    </tr>
    </table>

  <br><br>

  </div>

</div>

</body>
</html>


