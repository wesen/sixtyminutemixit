<?php

function mixitHead($title) {
?>
<html>
<head><title><?php echo "$title"; ?></title>
    <link rel="stylesheet" type="text/css" href="sixty.css"/>
</head>
<?php
}


function mixitList()
{
    global $mixit_config;
    $current_mixit = $mixit_config["current_mixit"];
    ?>

    <div class="sideModule" style="width:180px">
        <div class="left"><a href="create-mixit.php" class="rf">+</a>Mixits</div>
        <?php
        for ($i = $current_mixit; $i >= 0; $i--) {
            if (doesMixitExist($i) && isMixitValid($i)) {
                echo "<a href=\"mixit.php?mixit=$i\"><div class=\"sep small normall np\">\n";
                echo "<div class=\"sideBlogDig\" title=\"this is the number of submissions for this mixit\" style=\"background-color: #dddddd\">" . mixitSubmissions($i) . "</div>\n";
                echo "<div class=\"sideBlogTitle\">" . mixitTitle($i);
                if (isMixitFinished($i)) {
                    echo ": finished";
                } else if (isMixitStarted($i)) {
                    echo ": running!!!";
                } else {
                    echo ": " . mixitSamples($i) . " samples";
                }
                echo "</div>\n";
            }
        }
        ?>
    </div>
    <?php
}

function mixitHeader() {
?>
<body>

<div id="bodyWrap">
    <!-- shamelessly ripped form em411.com -->
    <table cellpadding="0" cellspacing="0" border="0" id="page">
        <tr class="mast">
            <td>
                <div class="mLogo"><a href="index.php"><strong>#em411@irc.esper.net</strong><br><span class="small">sixtyminutemixit impromptu competition</span></a>
                </div>
            </td>
        </tr>

        <tr>
            <td style="padding-top:0;">
                <div id="menu">
                    <a href="quixit-faq.txt">FAQ</a> &#149;
                    <a href="create-mixit.php">+Mixit</a> &#149;
                    <a href="upload-sample.php">+Sample</a> &#149;
                    <a href="upload-submission.php">+Submission</a> &#149;
                    <?php
                    if (loggedIn()) {
                        echo "<a href=\"logout.php\">Logout</a>\n";
                    } else {
                        echo "<a href=\"login.php\">Login</a>\n";
                    }
                    ?>
            </td>
        </tr>
        <tr>
            <td id="mainPageContent">

                <div style=padding:0;margin-bottom:10px;></div>

                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td class="sideColumn" style="width:25%;"><?php mixitList(); ?></td>
                        <td style="width:50%" valign="top">
                            <?php
                            }

                            function mixitFooter() {
                            ?>
                        </td>
                        <td style="width:25%;"/>
                    </tr>
                </table>

            </td>
        </tr>
    </table>
</div>
</body>
</html>
<?php
}

function mixitFrontBoxHeader($title, $url, $author)
{
    echo "<div class=\"frontBox\">\n";
    echo "<a href=\"$url\">\n";
    echo "<div class=\"frontHeader\">$title</div></a>\n";
    if ($author) {
        echo "<div class=\"frontAuthor\">$author</div>\n";
    }
    echo "<div class=\"frontText\"><span class=\"lh\">\n";
}

function mixitFrontBoxFooter()
{
    echo "</span></div></div>\n";
}

function mixitFrontBox($title, $url, $author, $content)
{
    mixitFrontBoxHeader($title, $url, $author);
    echo "$content";
    mixitFrontBoxFooter();
}

?>
