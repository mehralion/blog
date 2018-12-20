<?

/**
 * Created by PhpStorm.
 * User: Николай
 * Date: 10.04.14
 * Time: 15:24
 */
ini_set("display_errors", 1);
$oldStatus = trim(shell_exec('/www/default.sh old status'));
//$rusStatus = trim(shell_exec('/www/default.sh rus status'));

if($oldStatus == "false") {
//echo "Try 1\n";
    shell_exec('/www/default.sh old start');
}

//if($rusStatus == "false") {
//echo "Try 2\n";
//    shell_exec('/www/default.sh rus start');
//}
exit;

//echo "Start ez-php\n";
?>