<?php

if (!$iniPath = get_cfg_var('cfg_file_path')) {
    $iniPath = 'WARNING: not using a php.ini file';
}

echo "********************************\n";
echo "*                              *\n";
echo "*  Symfony requirements check  *\n";
echo "*                              *\n";
echo "********************************\n\n";
echo sprintf("php.ini used by PHP: %s\n\n", $iniPath);

echo "** WARNING **\n";
echo "*  The PHP CLI can use a different php.ini file\n";
echo "*  than the one used with your web server.\n";
if ('\\' == DIRECTORY_SEPARATOR) {
    echo "*  (especially on the Windows platform)\n";
}
echo "*  If this is the case, please ALSO launch this\n";
echo "*  utility from your web server.\n";
echo "** WARNING **\n";

// mandatory
echo_title("Mandatory requirements");
check(version_compare(phpversion(), '5.3.2', '>='), sprintf('Checking that PHP version is at least 5.3.2 (%s installed)', phpversion()), 'Install PHP 5.3.2 or newer (current version is '.phpversion(), true);
check(ini_get('date.timezone'), 'Checking that the "date.timezone" setting is set', 'Set the "date.timezone" setting in php.ini (like Europe/Paris)', true);
check(is_writable(__DIR__.'/../app/cache'), sprintf('Checking that app/cache/ directory is writable'), 'Change the permissions of the app/cache/ directory so that the web server can write in it', true);
check(is_writable(__DIR__.'/../app/logs'), sprintf('Checking that the app/logs/ directory is writable'), 'Change the permissions of the app/logs/ directory so that the web server can write in it', true);
check(function_exists('json_encode'), 'Checking that the json_encode() is available', 'Install and enable the json extension', true);
check(class_exists('SQLite3') || in_array('sqlite', PDO::getAvailableDrivers()), 'Checking that the SQLite3 or PDO_SQLite extension is available', 'Install and enable the SQLite3 or PDO_SQLite extension.', true);
check(function_exists('session_start'), 'Checking that the session_start() is available', 'Install and enable the session extension', true);
check(function_exists('ctype_alpha'), 'Checking that the ctype_alpha() is available', 'Install and enable the ctype extension', true);


/**
 * Checks a configuration.
 */
function check($boolean, $message, $help = '', $fatal = false)
{
    echo $boolean ? "  OK        " : sprintf("\n\n[[%s]] ", $fatal ? ' ERROR ' : 'WARNING');
    echo sprintf("$message%s\n", $boolean ? '' : ': FAILED');

    if (!$boolean) {
        echo "            *** $help ***\n";
        if ($fatal) {
            die("You must fix this problem before resuming the check.\n");
        }
    }
}

function echo_title($title)
{
    echo "\n** $title **\n\n";
}
