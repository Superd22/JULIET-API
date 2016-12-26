<?php
  require_once(__DIR__."/helpers.php");
  if(isset($_POST['eId'])) $eId = (int) $_POST['eId'];
  $can = $asked = $isIn = false;

  function ju_cal_invit_info_user($eId) {
    return \JULIET\Calendar\helper\aSummary::get_summary($eId);
  }
  
    //Phpbb::make_phpbb_env();
    if($user && isset($eId)) {
      $search = ju_cal_invit_info_user($eId);
      print_r(json_encode($search));
    }
?>
