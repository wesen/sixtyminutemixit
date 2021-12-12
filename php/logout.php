<?php

include("mixitlib.php");

if (loggedIn()) {
    setcookie("loginname", "", time() - 3600);
    setcookie("password", "", time() - 3600);
}

mixitHead("sixtyminutemixit");
mixitHeader();

mixitLogoutPage();

mixitFooter();

function mixitLogoutPage()
{
    if (loggedIn()) {
        mixitFrontBox("Logout successful", "index.php", 0, "Logout successful.");
    }
}

?>
