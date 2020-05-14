<?php

include("mixitlib.php");

$mixit = $_POST["mixit"] ? $_POST["mixit"] : $_GET["mixit"];
$current_mixit = $mixit_config["current_mixit"];
if (!$mixit)
  $mixit = $current_mixit;

preg_replace("/[^0-9]/", "", $mixit);

mixitHead("sixtyminutemixit : create a mixit");
mixitHeader();
mixitCreatePage();
mixitFooter();

function mixitCreateMixitPage() {
  global $mixit, $current_mixit, $login, $password;

  if (!mixitCheckLoginPage()) {
    return;
  }

/*
  if ($login != "manuel") {
     mixitFrontBox("Mixit admin", "", 0, "Only manuel can create mixits for now");
     return;
  }
  */

  $title = strip_tags($_POST["title"]);
  $description = strip_tags($_POST["description"]);
  $duration = strip_tags($_POST["duration"]);
  $blabla = strip_tags($_POST["blabl"]);
  preg_replace("/[^0-9]/", "", $duration);

  if ($blabla != "nospam") {
     mixitFrontBox("evil spammer", "create-mixit.php", 0, "evil spammer...");
     }

  if ($duration == "") {
    $duration = 60;
  }
  
  if ($title == "" || $description == "") {
    mixitFrontBox("Mixit Error", "create-mixit.php", 0,
		  "Please provide a title and a description for your mixit...");
    return;
  }
  
  for ($mixit = $current_mixit + 1; doesMixitExist($mixit); $mixit++)
    ;
  mixitFrontBoxHeader("Creating mixit $mixit...", "mixit.php?mixit=$mixit", $login);
  createMixit($mixit, $login, $title, $description, $duration);
  setCurrentMixit($mixit);
  
  echo "Mixit $mixit ".mixitTitle($mixit)." was created successfully. Administer the mixit at <a class=\"link\" href=\"create-mixit.php?mixit=$mixit\">the administration page</a>.";
  mixitFrontBoxFooter();
}

 function mixitSetDurationPage() {
   global $mixit, $login;

   if (!mixitCheckLoginPage()) {
     return;
   }

  if (!doesMixitExist($mixit)) {
    mixitFrontBox("Mixit $mixit does not exist!", "create-mixit.php", 0,
		  "Mixit $mixit does not exist!");
    return;
  }

  if (!isMixitValid($mixit)) {
    mixitFrontBox("Mixit $mixit is not valid", "create-mixit.php", 0,
		  "The mixit you chose is not valid, there should be a more recent one available.");
    return;
  }
    
  $creator = mixitCreator($mixit);
  if ($login == $creator) { 
    $duration = strip_tags($_POST["duration"]);
    preg_replace("/[^0-9]/", "", $duration);
    $old_duration = mixitDuration($mixit);
    if ($duration == "") {
      mixitFrontBox("Invalid duration", "create-mixit-php", 0,
		    "Please provide a valid new duration for the mixit.");
      return;
    }
    mixitSetDuration($mixit, $duration);
    $new_duration = mixitDuration($mixit);
   
    mixitFrontBox("Mixit duration updated", "mixit.php?mixit=$mixit", 0,
		  "Mixit $mixit \"".mixitTitle($mixit)." is now $new_duration minutes long. Post your submissions at <a href=\"upload-submission.php?mixit=$mixit\" class=\"link\">the submissions page</a>");
  } else {
    mixitFrontBox("Mixit error", "create-mixit.php", 0,
		  "Could not update the mixit $mixit duration because you are not the creator of the mixit.");
  }
   
 }

function mixitStartMixitPage() {
  global $mixit, $login;
  global $current_mixit;

  if (!mixitCheckLoginPage()) {
    return;
  }

  if (!doesMixitExist($mixit)) {
    mixitFrontBox("Mixit $mixit does not exist!", "create-mixit.php", 0,
		  "Mixit $mixit does not exist!");
    return;
  }
    
  $creator = mixitCreator($mixit);
  if ($login == $creator) {
    startMixit($mixit);
    mixitFrontBox("Mixit $mixit started", "mixit.php?mixit=$mixit", 0,
		  "Mixit $mixit \"".mixitTitle($mixit)." started. Post your submissions at <a href=\"upload-submission.php?mixit=$mixit\" class=\"link\">the submissions page</a>");
  } else {
    mixitFrontBox("Mixit error", "create-mixit.php", 0,
		  "Could not start the mixit $mixit because you are not the creator. $creator has to start the mixit.");
  }
}

function mixitStopSampleUpload() {
  global $mixit, $current_mixit, $login, $password;

  if (!mixitCheckLoginPage()) {
    return;
  }

  if (!doesMixitExist($mixit)) {
    mixitFrontBox("Mixit $mixit does not exist!", "create-mixit.php", 0,
		  "Mixit $mixit does not exist!");
    return;
  }
    
  $creator = mixitCreator($mixit);
  if ($login == $creator) {
    denySampleUpload($mixit);
      mixitFrontBox("Sample upload stopped", "mixit.php?mixit=$mixit", 0,
		    "Mixit $mixit sample upload is no more possible. Mixit samples can be downloaded at <a href=\"mixits/$mixit/samples/\" class=\"link\">the samples page</a>.");
  } else {
    mixitFrontBox("Mixit error", "create-mixit.php", 0,
		  "Could not stop sample upload for the mixit $mixit because you are not the creator. $creator has to stop sample uploading for the mixit.");
  }
}

function mixitStartSampleUpload() {
  global $mixit, $current_mixit, $login, $password;

  if (!mixitCheckLoginPage()) {
    return;
  }

  if (!doesMixitExist($mixit)) {
    mixitFrontBox("Mixit $mixit does not exist!", "create-mixit.php", 0,
		  "Mixit $mixit does not exist!");
    return;
  }
    
  $creator = mixitCreator($mixit);
  if ($login == $creator) {
    allowSampleUpload($mixit);
      mixitFrontBox("Sample upload allowed", "mixit.php?mixit=$mixit", 0,
		    "Mixit $mixit sample upload is activated. Mixit samples can be uploaded at <a href=\"upload-sample.php?mixit=$mixit\" class=\"link\">the sample upload page</a>.");
  } else {
    mixitFrontBox("Mixit error", "create-mixit.php", 0,
		  "Could not start sample upload for the mixit $mixit because you are not the creator. $creator has to stop sample uploading for the mixit.");
  }
}

function mixitFinalizeMixitPage() {
  global $mixit, $current_mixit, $login, $password;

  if (!mixitCheckLoginPage()) {
    return;
  }

  if (!doesMixitExist($mixit)) {
    mixitFrontBox("Mixit $mixit does not exist!", "create-mixit.php", 0,
		  "Mixit $mixit does not exist!");
    return;
  }
    
  $creator = mixitCreator($mixit);
  if ($login == $creator) {
      finalizeMixit($mixit);
      mixitFrontBox("Mixit finalized", "mixit.php?mixit=$mixit", 0,
		    "Mixit $mixit successfully finalized");
  } else {
    mixitFrontBox("Mixit error", "create-mixit.php", 0,
		  "Could not finalize the mixit $mixit because you are not the creator. $creator has to start the mixit.");
  }
      
}
function mixitCreatePage() {
  global $mixit, $current_mixit, $login, $password;
  
  if ($_POST["action"] == "mixit") {
    mixitCreateMixitPage();
  } else if ($_POST["action"] == "start") {
    mixitStartMixitPage();
  } else if ($_POST["action"] == "finalize") {
    mixitFinalizeMixitPage();
  } else if ($_POST["action"] == "setduration") {
    mixitSetDurationPage();
  } else if ($_POST["action"] == "stopsampleupload") {
    mixitStopSampleUpload();
  } else if ($_POST["action"] == "allowsampleupload") {
    mixitStartSampleUpload();
  }
  
  if (doesMixitExist($current_mixit) && !isMixitFinished($current_mixit)) {
    mixitFrontBox("Mixit on the way", "mixit.php?mixit=$current_mixit", 0,
		  "There is already a mixit underway, please participate to this one.");
  } else  {
    mixitFrontBoxHeader("Create a mixit", "create-mixit.php", 0);
    ?>
      Create a new mixit so that everyone can upload samples.<br/>
    <form action="create-mixit.php" method="POST">
      <table>
	<input type="hidden" name="action" value="mixit"/>
<?php if (!loggedIn()) { ?>
	<tr><td>Login:</td><td><input type="text" name="loginname" size="20"/></td></tr>
	<tr><td>Password:</td><td><input type="password" name="password" size="20"></td></tr>
<?php } ?>
	<tr><td>Title:</td><td><input type="text" name="title" size="50"/></td></tr>
<input type="hidden" name="blabla" value="nospam"/>
	<tr><td>Duration (min.):</td><td><input type="text" value="60" name="duration" size="20"/></td></tr>
	<tr><td>Description:</td><td><textarea name="description" cols="50" rows="10"></textarea></td></tr>
	<tr><td cols="2"><input type="submit" value="Mixit!"></td></tr>
	</table>
    </form>
	<?php
       mixitFrontBoxFooter();
  }

  if (($current_mixit == $mixit) && doesMixitExist($current_mixit) && !isMixitStarted($current_mixit)) {
    mixitFrontBoxHeader("Start the current mixit", "create-mixit.php", 0);
      ?>
      Start the current mixit <?php echo "$current_mixit \"".mixitTitle($current_mixit)."\""; ?> and make the <?php echo mixitSamples($current_mixit);?> uploaded samples available to everyone.
      <form action="create-mixit.php" method="POST">
	<table>
<input type="hidden" name="blabla" value="nospam"/>
	<input type="hidden" name="action" value="start"/>
<?php if (!loggedIn()) { ?>
	<tr><td>Login:</td><td><input type="text" name="loginname" size="20"/></td></tr>
	<tr><td>Password:</td><td><input type="password" name="password" size="20"></td></tr>
<?php } ?>
	<tr><td>Current Mixit:</td><td><?php echo "Mixit ".$current_mixit.": ".mixitDescription($current_mixit) ?></td></tr>
	<tr><td cols="2"><input type="submit" value="Start!"></td></tr>
	</table>
	</form>
	
	<?php
	 mixitFrontBoxFooter();
	
	}

  if (doesMixitExist($mixit)) {
      mixitFrontBoxHeader("Change the mixit duration", "create-mixit.php", 0);
      ?>
	Change the duration of the mixit <?php echo "$mixit \"".mixitTitle($mixit)."\""; ?>
	  <br/>
      <form action="create-mixit.php" method="POST">
	<table>
<input type="hidden" name="blabla" value="nospam"/>
	<input type="hidden" name="action" value="setduration"/>
	     <tr><td>Old duration was <?php echo mixitDuration($mixit) ?> minutes, new duration (in minutes):</td><td><input type="text" name="duration" size="20" value="<?php echo mixitDuration($mixit); ?>"/></td></tr>
<?php if (!loggedIn()) { ?>
	<tr><td>Login:</td><td><input type="text" name="loginname" size="20"/></td></tr>
	<tr><td>Password:</td><td><input type="password" name="password" size="20"></td></tr>
<?php } ?>
	<tr><td cols="2"><input type="submit" value="Change duration!"></td></tr>
	</table>
	</form>
	
	<?php
	 mixitFrontBoxFooter();
	
  if (isMixitSampleUploadAllowed($mixit)) {
      mixitFrontBoxHeader("Stop sample upload", "create-mixit.php", 0);
      ?>
	Stop sample upload for <?php echo "$mixit \"".mixitTitle($mixit)."\""; ?> and create the samplepack (command can go on for quite a while if there are a lot of smaples).
	  <br/>
      <form action="create-mixit.php" method="POST">
	<table>
<input type="hidden" name="blabla" value="nospam"/>
	<input type="hidden" name="action" value="stopsampleupload"/>
<?php if (!loggedIn()) { ?>
	<tr><td>Login:</td><td><input type="text" name="loginname" size="20"/></td></tr>
	<tr><td>Password:</td><td><input type="password" name="password" size="20"></td></tr>
<?php } ?>
	<tr><td cols="2"><input type="submit" value="Stop sample upload!"></td></tr>
	</table>
	</form>
	
	<?php
	 mixitFrontBoxFooter();
  } else {
      mixitFrontBoxHeader("Allow sample upload", "create-mixit.php", 0);
      ?>
	Allow sample upload for <?php echo "$mixit \"".mixitTitle($mixit)."\""; ?>
	  <br/>
      <form action="create-mixit.php" method="POST">
	<table>
<input type="hidden" name="blabla" value="nospam"/>
	<input type="hidden" name="action" value="allowsampleupload"/>
<?php if (!loggedIn()) { ?>
	<tr><td>Login:</td><td><input type="text" name="loginname" size="20"/></td></tr>
	<tr><td>Password:</td><td><input type="password" name="password" size="20"></td></tr>
<?php } ?>
	<tr><td cols="2"><input type="submit" value="Allow sample upload!"></td></tr>
	</table>
	</form>
	
	<?php
	 mixitFrontBoxFooter();
  }
  }
    
    if (doesMixitExist($mixit) && isMixitFinished($mixit)) {
      mixitFrontBoxHeader("Finalize the mixit", "create-mixit.php", 0);
      ?>
	Finalize the mixit <?php echo "$mixit \"".mixitTitle($mixit)."\""; ?>
        and make the <?php echo mixitSubmissions($mixit);?> uploaded submissions available to everyone!<br/>
      <form action="create-mixit.php" method="POST">
	<table>
<input type="hidden" name="blabla" value="nospam"/>
	<input type="hidden" name="action" value="finalize"/>
<?php if (!loggedIn()) { ?>
	<tr><td>Login:</td><td><input type="text" name="loginname" size="20"/></td></tr>
	<tr><td>Password:</td><td><input type="password" name="password" size="20"></td></tr>
<?php } ?>
	<tr><td cols="2"><input type="submit" value="Finalize!"></td></tr>
	</table>
	</form>
	
	<?php
	 mixitFrontBoxFooter();
	}
}

?>
