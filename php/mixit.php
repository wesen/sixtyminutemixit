<?php

include("mixitlib.php");

mixitHead("sixtyminutemixit");
mixitHeader();
mixitMixitPage();
mixitFooter();

function mixitMixitPage()
{
    global $mixit_config;

    $current_mixit = $mixit_config["current_mixit"];

    $mixit = $_POST["mixit"] ? $_POST["mixit"] : $_GET["mixit"];
    //  $mixit = $_GET["mixit"];
    $current_mixit = $mixit_config["current_mixit"];
    if (!$mixit)
        $mixit = $current_mixit;

    preg_replace("/[^0-9]/", "", $mixit);

    if (!doesMixitExist($mixit)) {
        mixitFrontBox("No such mixit", "create-mixit.php", 0,
            "There is no such mixit yet, please create one at <a class=\"link\" href=\"create-mixit.php\">the mixit creation page</a>.");
        return;
    }

    mixitFrontBox("Mixit $mixit \"" . mixitTitle($mixit) . "\"", "mixit.php?mixit=$mixit", mixitCreator($mixit),
        mixitDescription($mixit));

    if (isMixitFinished($mixit)) {
        mixitFrontBox("Mixit is finished", "mixits/$mixit/submissions/", mixitCreator($mixit),
            "The mixit $mixit is finished, go get the " . mixitSubmissions($mixit) . " submissions at <a href=\"mixits/$mixit/submissions/\" class=\"link\">the submissions page</a> and the " . mixitSamples($mixit) . " uploaded samples at <a href=\"mixits/$mixit/samples/\" class=\"link\">the samples page</a>.");
    } else if (isMixitStarted($mixit)) {
        mixitFrontBox("Mixit is running", "upload-submission.php?mixit=$mixit", mixitCreator($mixit),
            "The mixit $mixit is currently running. There are " . formatTime(mixitSecsLeft($mixit)) . " left.<br/>" .
            "Get the <a class=\"link\" href=\"mixits/$mixit/samples/\">samples</a> and participate. Upload your <a class=\"link\" href=\"upload-submission.php?mixit=$mixit\">submission</a>. There are already " . mixitSubmissions($mixit) . " uploaded submissions.<br/>");
    } else if (!isMixitStarted($mixit)) {
        if (isMixitSampleUploadAllowed($mixit)) {
            mixitFrontBox("Mixit not started", "upload-sample.php?mixit=$mixit", mixitCreator($mixit),
                "The mixit $mixit has not started yet. Upload your samples at <a href=\"upload-sample.php?mixit=$mixit\" class=\"link\">the samples upload page</a> and participate! There are already " . mixitSamples($mixit) . " uploaded samples.");
        } else {
            mixitFrontBox("Mixit not started", "mixits/$mixit/samples", mixitCreator($mixit),
                "The mixit $mixit has not started yet, but sample uploading has been closed. You can download the samples at <a href=\"mixits/$mixit/samples/\" class=\"link\">the samples repository</a>. The smaplepack for the mixit can be found at <a href=\"mixits/$mixit/samples/smaplepack-quixit-$mixit.zip\" class=\"link\">the smaplepack</a>. There are " . mixitSamples($mixit) . " uploaded samples.");
        }
    }

    mixitFrontBox("Manage this mixit", "create-mixit.php?mixit=$mixit", 0,
        "Manage the mixit $mixit at <a href=\"create-mixit.php?mixit=$mixit\" class=\"link\">the admin page</a>.");
}
    
