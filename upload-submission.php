<?php

include("mixitlib.php");

$current_mixit = $mixit_config["current_mixit"];

$mixit = $_POST["mixit"];
$current_mixit = $mixit_config["current_mixit"];
if (!$mixit)
  $mixit = $current_mixit;

preg_replace("/[^0-9]/", "", $mixit);



mixitHead("sixtyminutemixit - upload your submission");
mixitHeader();
mixitUploadSubmissionPage();
mixitFooter();

function mixitUploadPage() {
    global $mixit, $current_mixit, $login, $password;
  
    if (!mixitCheckLoginPage()) {
      return;
    }
    
    if (!$_FILES["submission"]) {
      mixitFrontBox("No submission submitted", "upload-submission.php?mixit=$mixit", 0,
		    "Please upload a submission.");
      return;
    }
    
    checkMixitDir($mixit);
    
    if (isMixitFinished($mixit)) {
      mixitFrontBox("Mixit finished", "mixit.php?mixit=$mixit", 0,
		    "The mixit is already finished, visit the <a href=\"mixit.php?mixit=$mixit\" class=\"link\">mixit page</a>.");
      return;
    } else if (!isMixitStarted($mixit)) {
      mixitFrontBox("Mixit not started", "mixit.php?mixit=$mixit", 0,
		    "The mixit is not yet started, visit the <a href=\"mixit.php?mixit=$mixit\" class=\"link\">mixit page</a>.");
      return;
    }

    $submissionname = "$login--".basename($_FILES["submission"]["name"]);
    if (checkIfSubmissionExists($mixit, $submissionname)) {
      mixitFrontBox("Submission already exists", "upload-submission.php?mixit=$mixit", 0,
		    "Submission $submissionname already exists, please choose another name.");
      return;
    }
    
    if (filesize($_FILES["submission"]["tmp_name"]) > 10000000) {
      mixitFrontBox("Submission too large", "upload-submission.php?mixit=$mixit", 0,
		    "The submission is too large, a submission cannot be larger than 10 MB!");
      return;
    }
    
    $targetfile = getMixitDir($mixit)."/submissions/$submissionname";
    if (move_uploaded_file($_FILES["submission"]["tmp_name"], $targetfile)) {
      mixitFrontBox("Submission added", "upload-submission.php?mixit=$mixit", 0,
		    "Your submission $submissionname was correctly added to mixit $mixit, thank you.");
    } else {
      mixitFrontBox("Submission upload error", "upload-submission.php?mixit=$mixit", 0,
		    "There was an error while uploading submission $submissionname, please try again.");
    }
}

function mixitUploadSubmissionPage() {
  global $mixit, $current_mixit, $login, $password;

  if (!doesMixitExist($mixit)) {
    mixitFrontBox("No such mixit", "create-mixit.php", 0, 
		  "There is no such mixit yet, please create one at <a class=\"link\" href=\"create-mixit.php\">the mixit creation page</a>.");
    return;
  }
  
  if ($_POST["action"] == "upload") {
    mixitUploadPage();
  }

  if (isMixitFinished($mixit)) {
    mixitFrontBox("Mixit finished", "mixit.php?mixit=$mixit", 0, 
		  "The mixit nr. $mixit is finished, go get the submissions at <a href=\"mixits/$mixit/submissions/\" class=\"link\">the submissions directory</a>.");
  } else if (!isMixitStarted($mixit)) {
    mixitFrontBox("Mixit is not yet started", "upload-sample.php?mixit=$mixit", 0,
		  "The mixit has already started, go upload samples at <a class=\"link\" href=\"upload-sample.php?mixit=$mixit\">the sample upload page</a>.");
  } else {
    mixitFrontBoxHeader("Upload a submission", "upload-submission.php?mixit=$mixit", 0);
    ?>
      Upload a submission (max. 10 MB) for the mixit <?php echo "$mixit \"".mixitTitle($mixit)."\"" ?>.<br/>
    <form enctype="multipart/form-data" action="upload-submission.php" method="POST">
      <table>
      <input type="hidden" name="action" value="upload"/>
<?php if (!loggedIn()) { ?>
      <tr><td>Login:</td><td><input type="text" name="loginname" size="20"/></td></tr>
      <tr><td>Password:</td><td><input type="password" name="password" size="20"></td></tr>
<?php } ?>
      <tr><td>Track (max. 10 MB):</td><td><input type="file" name="submission"/></td></tr>
      <tr><td cols="2"><input type="submit" value="Upload!"></td></tr>
      </table>
   </form>
   <?php
    mixitFrontBoxFooter();						 
 }
}
?>

</body>
</html>
