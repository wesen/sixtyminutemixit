<?php

include("mixitlib.php");

mixitHead("sixtyminutemixit");
mixitHeader();

$current_mixit = $mixit_config["current_mixit"];

mixitFrontBox("Welcome to sixty minute mixit!", "index.php", 0,
    "Welcome to the em411.com sixty minute mixit, which will take sporadically. You can create an account at the <a href=\"login.php\" class=\"link\">login page</a>, and create a mixit at the <a href=\"create-mixit.php\" class=\"link\">mixit creation page</a>. Have fun and meet us in #em411 on efnet.");

if (doesMixitExist($current_mixit)) {
    $text = mixitDescription($current_mixit);
    if (isMixitStarted($current_mixit) && !isMixitFinished($current_mixit)) {
        $left = mixitSecsLeft($current_mixit);
        if (($left / 60) > 1)
            $time = ($left / 60) . " minutes and " . ($left % 60) . " seconds";
        else
            $time = "$left seconds";

        $text .= "<br/><hr/>The mixit is running!!! " . formatTime($left) . " left.<br/>";
        $text .= "Get the <a class=\"link\" href=\"mixits/$current_mixit/samples/\">samples</a> and participate. Upload your <a class=\"link\" href=\"upload-submission.php?mixit=$current_mixit\">submission</a>.<br/>";
    } else if (isMixitFinished($current_mixit)) {
        $text .= "<br/><hr/>The mixit is finished. Get the <a class=\"link\" href=\"mixits/$current_mixit/submissions/\">submissions</a>.<br/>";
    }

    mixitFrontBox("Current mixit $current_mixit: " . mixitTitle($current_mixit),
        "mixit.php?mixit=$current_mixit",
        mixitCreator($current_mixit),
        $text);

}

mixitFooter();
?>
