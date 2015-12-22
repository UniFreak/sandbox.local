<?php
function eVar($var) {
    echo $var . '<br />';
}
function dVar($var) {
    var_dump($var);
}

/* ==================== Mysqli ==================== */
$db = mysqli_connect('127.0.0.1', 'root', '', 'hwp');
dVar($db);
dVar(mysqli_ping($db));

$userInput =  '1';
// you can pass a third param `MYSQLI_USE_RESULT` to use unbuffered query
// difference:
//  buffered query:
//      1. php must wait while the entire quer is excuted and returned before it can process
//      2. in order to return the whole result to php at once, all data must be held in RAM
//  unbuffered query:
//      1. php script can parse the reuslt immediatly, giving immediat feedback to users
//      2. only one raw at a time need be held in RAM
//      3. if you issue another query before you finish all the rows from the previous query
//         php will issue a warning
//      4. functions such as mysqli_num_rows() only return the numbers of rows *so far*
//      5. between the time you start your unbuffered query and your processing the last row
//         the table remain locked
$result = mysqli_query(
            $db,
            "SELECT * FROM user WHERE id = " . mysqli_real_escape_string($db, $userInput)
        );
$resultClone = $result;
dVar($result);
eVar('there are ' . mysqli_num_rows($result) . ' returned');
while ($row = mysqli_fetch_assoc($result)) {
    dVar($row);
}

$insert = mysqli_query(
    $db,
    "INSERT INTO user VALUES (null, 'script', 'inserted', 21, 'femal');"
    );
eVar('last inserted id is: ' . mysqli_insert_id($db));
// use those two function to save resource if there will be some time between
// your last database use and your script ending
mysqli_free_result($result);
mysqli_close($db);

/* ==================== PEAR::DB ==================== */
// it's suspended, use MDB2
// require 'includes/DB.php';

// $dns = 'mysqli://root:@127.0.0.1/hwp';
// $db = DB::connect($dns);
// dVar($db);

// if (DB::isError($db)) {
//     dVar($db->getMessage());
// }

// $result = $db->query('SELECT * FROM user');
// while ($result->fetchInto($row, DB_FETCHMODE_ASSOC)) {
//     dVar($row);
// }

// $result->free();
// $db->discounnect();