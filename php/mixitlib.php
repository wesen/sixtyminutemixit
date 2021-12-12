<?php

include("mixitconfig.php");

if (isset($_POST["loginname"])) {
    $login = strip_tags($_POST["loginname"]);
} else if (isset($_COOKIE["loginname"])) {
    $login = strip_tags($_COOKIE["loginname"]);
}
if (isset($_POST["password"])) {
    $password = strip_tags($_POST["password"]);
} else if (isset($_COOKIE["password"])) {
    $password = strip_tags($_COOKIE["password"]);
}

function check_perms($file, $perms)
{
    clearstatcache();
    $configmod = substr(sprintf('%o', fileperms($path)), -4);
    return ($configmod == $perms);
}

$mixit_config_loaded = 0;
$mixit_config = array();
$mixit_logins_loaded = 0;
$mixit_logins = array();

include("mixitlayout.php");

function loadConfig()
{
    global $mixit_config_loaded, $mixit_config, $mixit_config_file;
    if ($mixit_config_loaded == 1)
        return;
    $handle = fopen($mixit_config_file, "r");
    while ($string = fgetss($handle, 200)) {
        $string = trim($string);
        list($name, $val) = preg_split("/=/", $string);
        if ($name) {
            $mixit_config[$name] = $val;
            // echo "Read :$name: and :$val:\n";
        }
    }
    fclose($handle);
    $mixit_config_loaded = 1;
}

function writeConfig()
{
    global $mixit_config_loaded, $mixit_config, $mixit_config_file;
    if (!$mixit_config_loaded)
        return;
    $handle = fopen($mixit_config_file, "w");
    foreach ($mixit_config as $name => $val) {
        fwrite($handle, "$name=$val\n");
    }
    fclose($handle);
}

function initConfig()
{
    global $mixit_config, $initial_login_file, $initial_mixits_dir;
    loadConfig();
    if (!$mixit_config["config_init"]) {
        $mixit_config["config_init"] = 1;
        $mixit_config["current_mixit"] = 0;
        $mixit_config["current_mixit_phase"] = "none";
        $mixit_config["login_file"] = $initial_login_file;
        $mixit_config["mixit_dir"] = $initial_mixits_dir;
        writeConfig();
    }
}

// initConfig();
loadConfig();

function loadLogins()
{
    global $mixit_logins_loaded, $mixit_config, $mixit_logins;
    $mixit_login_file = $mixit_config["login_file"];
    if ($mixit_logins_loaded == 1)
        return;
    $handle = fopen($mixit_login_file, "r");
    while ($string = fgetss($handle, 200)) {
        $string = trim($string);
        list($login, $pass) = preg_split("/\s+/", $string);
        if ($login && $pass) {
            $mixit_logins[$login] = $pass;
            //      echo "reading: $login : $pass<br/>";
        }
    }
    fclose($handle);
    $mixit_logins_loaded = 1;
}

function writeLogins()
{
    global $mixit_logins_loaded, $mixit_config, $mixit_logins;
    $mixit_login_file = $mixit_config["login_file"];
    if (!$mixit_logins_loaded)
        return;
    $handle = fopen($mixit_login_file, "w");
    foreach ($mixit_logins as $login => $pass) {
        fwrite($handle, "$login $pass\n");
    }
    fclose($handle);
}

function checkLogin($login, $password)
{
    global $mixit_logins;
    loadLogins();
    return ($mixit_logins[$login] == $password);
}

function loggedIn()
{
    $login = $_COOKIE["loginname"];
    $password = $_COOKIE["password"];
    if (isset($login) && isset($password))
        return checkLogin($login, $password);
    else
        return false;
}

function loginExists($login)
{
    global $mixit_logins;
    loadLogins();
    if ($mixit_logins[$login]) {
        return 1;
    } else {
        return 0;
    }
}

function mixitCheckLoginPage()
{
    global $login, $password;

    if (!loggedIn() && !checkLogin($login, $password)) {
        mixitFrontBox("Login Error", "login.php", 0, "Could not verify login, please create a login at <a class=\"link\" href=\"login.php\">the login page</a>");
        return 0;
    }

    return 1;
}

loadLogins();

function file_write_complete($file, $content)
{
    $handle = fopen($file, "w");
    fwrite($handle, $content);
    fclose($handle);
}

function file_read_one_line($file)
{
    $handle = fopen($file, "r");
    if (!$handle)
        return false;
    $string = fgetss($handle, 200);
    $string = trim($string);
    fclose($handle);
    return $string;
}

function file_read_complete($file)
{
    $handle = fopen($file, "r");
    if (!$handle)
        return false;
    $string = "";
    while ($res = fgetss($handle, 200)) {
        $string = $string . $res;
    }
    fclose($handle);
    return $string;
}

function dir_count_files($dir)
{
    $handle = opendir($dir);
    $count = 0;
    while (false !== ($file = readdir($handle))) {
        if (is_file($dir . "/" . $file) && ($file[0] != ".") && $file !== '.' && $file !== '..') {
            $count++;
        }
    }
    return $count;
}

function getMixitDir($mixit)
{
    global $mixit_config;

    $dir = $mixit_config["mixit_dir"] . "/$mixit";
    return $dir;
}

function intDiv($x, $y)
{
    $t = 1;
    if ($y == 0 || $x == 0)
        return 0;
    if ($x < 0 xor $y < 0) //Mistaken the XOR in the last instance...
        $t = -1;
    $x = abs($x);
    $y = abs($y);
    $ret = 0;
    while (($ret + 1) * $y <= $x)
        $ret++;
    return $t * $ret;
}

function formatTime($secs)
{
    $minutes = intDiv($secs, 60);
    $hours = intDiv($minutes, 60);
    $days = intDiv($hours, 24);

    $res = "";
    if ($days > 0) {
        $res = "$days days, ";
        $hours -= $days * 24;
    }
    if ($hours > 0) {
        $res .= "$hours hours, ";
        $minutes -= $hours * 60;
    }
    if ($minutes > 0) {
        $res .= "$minutes minutes, ";
        $secs -= $minutes * 60;
    }
    $res .= "$secs seconds";
    return $res;
}

function checkMixitDir($mixit)
{
    if (!doesMixitExist($mixit)) {
        $dir = getMixitDir($mixit);
        if (!mkdir($dir)) {
            echo "Could not create mixit dir \"$dir\"\n";
            return;
        }
        mkdir("$dir/samples");
        mkdir("$dir/submissions");
        denyMixitSubmissions($mixit);
        denyMixitSamples($mixit);
    }
}

function doesMixitExist($mixit)
{
    $dir = getMixitDir($mixit);
    return file_exists($dir);
}

function isMixitValid($mixit)
{
    global $mixit_config;
    if (!isMixitStarted($mixit) && ($mixit != $mixit_config["current_mixit"])) {
        return 0;
    } else {
        return 1;
    }
}

function isMixitSampleUploadAllowed($mixit)
{
    if (!doesMixitExist($mixit))
        return false;
    $samples_upload = file_read_complete(getMixitDir($mixit) . "/samples-upload.txt");
    if ($samples_upload == "true")
        return true;
    else
        return false;
}

function isMixitStarted($mixit)
{
    return (doesMixitExist($mixit) && (file_exists(getMixitDir($mixit) . "/start-time.txt")));
}

function isMixitFinished($mixit)
{
    return (isMixitStarted($mixit) && (mixitSecsSinceStart($mixit) > (mixitDuration($mixit) * 60)));
}

function createMixit($mixit, $creator, $title, $description, $duration)
{
    checkMixitDir($mixit);
    $dir = getMixitDir($mixit);
    file_write_complete("$dir/creator.txt", $creator);
    file_write_complete("$dir/title.txt", $title);
    file_write_complete("$dir/duration.txt", $duration);
    file_write_complete("$dir/description.txt", nl2br($description));
    file_write_complete("$dir/samples-upload.txt", "true");
}

function mixitSetDuration($mixit, $duration)
{
    checkMixitDir($mixit);
    $dir = getMixitDir($mixit);
    file_write_complete("$dir/duration.txt", $duration);
}

function setCurrentMixit($mixit)
{
    global $mixit_config;

    $mixit_config["current_mixit"] = $mixit;
    writeConfig();
}

function denySampleUpload($mixit)
{
    checkMixitDir($mixit);
    file_write_complete(getMixitDir($mixit) . "/samples-upload.txt",
        "false");
    allowMixitSamples($mixit);
    createSamplePack($mixit);
}

function createSamplePack($mixit)
{
    $mixitdir = getMixitDir($mixit) . "/samples/";
    exec("/home/manuel/public_html/sixtyminutemixit/create-mixit-sample-pack.sh \"$mixit\" \"$mixitdir\" &");
}

function createSubmissionPack($mixit)
{
    $mixitdir = getMixitDir($mixit) . "/submissions/";
    exec("/home/manuel/public_html/sixtyminutemixit/create-mixit-submission-pack.sh \"$mixit\" \"$mixitdir\" &");
}


function allowSampleUpload($mixit)
{
    checkMixitDir($mixit);
    denyMixitSamples($mixit);
    file_write_complete(getMixitDir($mixit) . "/samples-upload.txt",
        "true");
}

function startMixit($mixit)
{
    checkMixitDir($mixit);
    list($bla, $starttime) = explode(" ", microtime());
    file_write_complete(getMixitDir($mixit) . "/start-time.txt", $starttime);
    denySampleUpload($mixit);
    allowMixitSamples($mixit);
    denyMixitSubmissions($mixit);
}

function finalizeMixit($mixit)
{
    allowMixitSamples($mixit);
    allowMixitSubmissions($mixit);
    createSubmissionPack($mixit);
}

function mixitStartTime($mixit)
{
    return file_read_one_line(getMixitDir($mixit) . "/start-time.txt");
}

function mixitDuration($mixit)
{
    return file_read_one_line(getMixitDir($mixit) . "/duration.txt");
}

function mixitEndTime($mixit)
{
    return mixitStartTime($mixit) + mixitDuration($mixit) * 60;
}

function mixitDescription($mixit)
{
    return file_read_complete(getMixitDir($mixit) . "/description.txt");
}

function mixitCreator($mixit)
{
    return file_read_one_line(getMixitDir($mixit) . "/creator.txt");
}

function mixitSubmissions($mixit)
{
    return dir_count_files(getMixitDir($mixit) . "/submissions");
}

function mixitSamples($mixit)
{
    return dir_count_files(getMixitDir($mixit) . "/samples/");
}

function mixitTitle($mixit)
{
    return file_read_one_line(getMixitDir($mixit) . "/title.txt");
}

function mixitSecsSinceStart($mixit)
{
    list($usec, $sec) = explode(" ", microtime());
    //  echo "sec: $sec, start: " .mixitStartTime($mixit)."<br/>";
    return $sec - mixitStartTime($mixit);
}

function mixitSecsLeft($mixit)
{
    $res = mixitDuration($mixit) * 60 - mixitSecsSinceStart($mixit);
    if ($res < 0)
        return 0;
    else
        return $res;
}

function mixitGetSubmissionsList($mixit)
{
    checkMixitDir($mixit);
    $dir = getMixitDir($mixit) . "/submissions/";
    echo "submissions: $dir<br/>";
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            if (is_file($dir . $file) && !ereg("^\.", $file) && !ereg("\.zip$", $file)) {
                $result[] = $file;
            }
        }
        closedir($dh);
    }
    return $result;
}

function mixitDoesSubmissionExist($mixit, $submission)
{
    if (!doesMixitExist($mixit))
        return false;
    $submission = ereg_replace("[/\.]", "_");
    $file = getMixitDir($mixit) . "/submissions/$submission";
    return file_exists($file);
}

function mixitCreateSubmissionDirs($mixit, $submission)
{
    $submission = ereg_replace("[/\.]", "_");
    if (!mixitDoesSubmissionExist($mixit, $submission))
        return;
    $dir = getMixitDir($mixit);
    mkdir("$dir/$submission" . "_comments");
    mkdir("$dir/$submission" . "_points");
}

function mixitGetSubmissionCommentDir($mixit, $submission)
{
    $submission = ereg_replace("[/\.]", "_");
    mixitCreateSubmissionDirs($mixit, $submission);
    $dir = getMixitDir($mixit);
    return "$dir/$submission" . "_comments";
}

function denyMixitSubdir($mixit, $subdir)
{
    checkMixitDir($mixit);
    file_write_complete(getMixitDir($mixit) . "/$subdir/.htaccess", "deny from all\n");
}

function allowMixitSubdir($mixit, $subdir)
{
    checkMixitDir($mixit);
    file_write_complete(getMixitDir($mixit) . "/$subdir/.htaccess", "allow from all\n");
}

function allowMixitSamples($mixit)
{
    allowMixitSubdir($mixit, "samples");
}

function allowMixitSubmissions($mixit)
{
    allowMixitSubdir($mixit, "submissions");
}

function denyMixitSamples($mixit)
{
    denyMixitSubdir($mixit, "samples");
}

function denyMixitSubmissions($mixit)
{
    denyMixitSubdir($mixit, "submissions");
}

function checkIfSampleExists($mixit, $sample)
{
    $file = getMixitDir($mixit) . "/samples/$sample";
    return file_exists($file);
}

function checkIfSubmissionExists($mixit, $submission)
{
    $file = getMixitDir($mixit) . "/submission/$submission";
    return file_exists($file);
}

?>
