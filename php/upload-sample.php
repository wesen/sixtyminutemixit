<?php

include("mixitlib.php");

$current_mixit = $mixit_config["current_mixit"];

$mixit = $_POST["mixit"];
$current_mixit = $mixit_config["current_mixit"];
if (!$mixit)
    $mixit = $current_mixit;

preg_replace("/[^0-9]/", "", $mixit);


mixitHead("sixtyminutemixit - upload samples");
mixitHeader();
mixitUploadSamplesPage();
mixitFooter();

function mixitUploadPage()
{
    global $mixit, $current_mixit, $login, $password;

    if (!mixitCheckLoginPage()) {
        return;
    }

    if (!$_FILES["sample"]) {
        mixitFrontBox("No sample submitted", "upload-sample.php?mixit=$mixit", 0,
            "Please upload a sample.");
        return;
    }

    checkMixitDir($mixit);

    if (isMixitFinished($mixit)) {
        mixitFrontBox("Mixit finished", "mixit.php?mixit=$mixit", 0,
            "The mixit is already finished, visit the <a href=\"mixit.php?mixit=$mixit\" class=\"link\">mixit page</a>.");
        return;
    } else if (isMixitStarted($mixit)) {
        mixitFrontBox("Mixit started", "mixit.php?mixit=$mixit", 0,
            "The mixit is already started, visit the <a href=\"mixit.php?mixit=$mixit\" class=\"link\">mixit page</a>.");
        return;
    } else if (!isMixitSampleUploadAllowed($mixit)) {
        mixitFrontBox("Sample upload not allowed", "mixit.php?mixit=$mixit", 0,
            "The uploading of samples to this mixit is not allowed anymore, but you can download the samples at <a href=\"mixit.php?mixit=$mixit\" class=\"link\">the mixit page</a>.");
        return;
    }

    $samplename = "$login--" . basename($_FILES["sample"]["name"]);
    if (checkIfSampleExists($mixit, $samplename)) {
        mixitFrontBox("Sample already exists", "upload-sample.php?mixit=$mixit", 0,
            "Sample $samplename already exists, please choose another name.");
        return;
    }

    if (filesize($_FILES["sample"]["tmp_name"]) > 5000000) {
        mixitFrontBox("Sample too large", "upload-sample.php?mixit=$mixit", 0,
            "The sample is too large, a sample cannot be larger than 5 MB!");
        return;
    }

    $targetfile = getMixitDir($mixit) . "/samples/$samplename";
    if (move_uploaded_file($_FILES["sample"]["tmp_name"], $targetfile)) {
        mixitFrontBox("Sample added", "upload-sample.php?mixit=$mixit", 0,
            "Your sample $samplename was correctly added to mixit $mixit, thank you.");
    } else {
        mixitFrontBox("Sample upload error", "upload-sample.php?mixit=$mixit", 0,
            "There was an error while uploading sample $samplename, please try again.");
    }
}

function mixitUploadSamplesPage()
{
    global $mixit, $current_mixit, $login, $password;

    if (!doesMixitExist($mixit)) {
        mixitFrontBox("No such mixit", "create-mixit.php", 0,
            "There is no such mixit yet, please create one at <a class=\"link\" href=\"create-mixit.php\">the mixit creation page</a>.");
        return;
    }

    if (!isMixitSampleUploadAllowed($mixit)) {
        mixitFrontBox("Sample upload denied", "create-mixit.php", 0,
            "Sample uploading is no more possible for this mixit. Please download the samples at <a href=\"mixits/$mixit/samples/\">the samples page</a> and wait for the mixit to start</a>");
        return;
    }

    if ($_POST["action"] == "upload") {
        mixitUploadPage();
    }

    if (isMixitFinished($mixit)) {
        mixitFrontBox("Mixit finished", "mixit.php?mixit=$mixit", 0,
            "The mixit nr. $mixit is finished, go get the submissions at <a href=\"mixits/$mixit/submissions/\" class=\"link\">the submissions directory</a>.");
    } else if (isMixitStarted($mixit)) {
        mixitFrontBox("Mixit started", "mixit.php?mixit=$mixit", 0,
            "The mixit has already started, go get the samples at <a class=\"link\" href=\"mixits/$mixit/samples/\">the samples directory</a> and upload your submission at <a href=\"upload-submission.php?mixit=$current_mixit\" class=\"link\">the submission page</a>.");
    } else {
        mixitFrontBoxHeader("Upload a sample", "upload-sample.php?mixit=$mixit", 0);
        ?>
        Upload a sample (max. 5 MB) for the mixit <?php echo "$mixit \"" . mixitTitle($mixit) . "\"" ?>.<br/>
        <form enctype="multipart/form-data" action="upload-sample.php" method="POST">
            <table>
                <input type="hidden" name="action" value="upload"/>
                <?php if (!loggedIn()) { ?>
                    <tr>
                        <td>Login:</td>
                        <td><input type="text" name="loginname" size="20"/></td>
                    </tr>
                    <tr>
                        <td>Password:</td>
                        <td><input type="password" name="password" size="20"></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td>Sample File (max 5 MB):</td>
                    <td><input type="file" name="sample"/></td>
                </tr>
                <tr>
                    <td cols="2"><input type="submit" value="Upload!"></td>
                </tr>
            </table>
        </form>
        <?php
        mixitFrontBoxFooter();
    }
}

?>
