<?php
// Settings..
define('db_host',"localhost");
define('db_name',"owncloud");
define('db_user',"owncloud-user");
define('db_pass',"owncloudpassword");
define('user_id',"owncloud_user");
define('db_prefix',"oc_");

$xmlfile = "homebank-file.xhb";

function db_getBank ($db, $name)
{
    $sql = "SELECT id FROM ".db_prefix."personalfinances_banks WHERE name='".$name."' AND user_id='".user_id."'";
    $ret = $db->query($sql);
    if ($ret) {
        $row =$ret->fetch();
        return $row['id'];
    }
    else
        return 0;
}

function db_insertBank ($db, $name)
{
    $sql = "INSERT INTO ".db_prefix."personalfinances_banks (name, user_id) VALUES ('".$name."','".user_id."')";
    $ret = $db->exec($sql);
    return $db->lastInsertId();
}

function db_getAccount ($db, $name, $bankname)
{
    $bank_id = db_getBank($db, $bankname);

    $sql = "SELECT id FROM ".db_prefix."personalfinances_accounts WHERE";
    $sql.= " name='".$name."'";
    $sql.= " AND bank_id='".$bank_id."'";
    $sql.= " AND user_id='".user_id."'";

    $ret = $db->query($sql);
    if ($ret) {
        $row =$ret->fetch();
        return $row['id'];
    }
    else
        return 0;
}

function db_insertAccount($db, $name, $bankname, $type, $initial)
{
    $bank_id = db_getBank($db, $bankname);

    $sql = "INSERT INTO ".db_prefix."personalfinances_accounts";
    $sql.= " (name, bank_id, type, initial, user_id) VALUES ";
    $sql.= "('".$name."','".$bank_id."','".$type."','".$initial."','".user_id."')";

    $ret = $db->exec($sql);
    return $db->lastInsertId();
}

function db_getCategory ($db, $name)
{
    $sql = "SELECT id FROM ".db_prefix."personalfinances_categories WHERE";
    $sql.= " name='".$name."'";
    $sql.= " AND user_id='".user_id."'";

    $ret = $db->query($sql);
    if ($ret) {
        $row =$ret->fetch();
        return $row['id'];
    }
    else
        return 0;
}

function db_insertCategory ($db, $name, $parent_id = 0)
{
    $sql = "INSERT INTO ".db_prefix."personalfinances_categories ";
    $sql.= "(name, parent, user_id) VALUES ";
    $sql.= "('" . $name . "', '" . $parent_id . "', '" . user_id . "')";

    $ret = $db->exec($sql);
    return $db->lastInsertId();
}

function db_getTransactionByDate ($db, $time)
{
    $sql = "SELECT id FROM ".db_prefix."personalfinances_transactions WHERE";
    $sql.= " date=" . $time;
    $sql.= " AND user_id='".user_id."'";

    $ret = $db->query($sql);
    if ($ret) {
        $row =$ret->fetch();
        return $row['id'];
    }
    else
        return 0;
}

function db_insertTransaction ($db, $date, $amount, $account_id, $dst_account_id, $paymode, $flags, $category_id, $info, $id_kxfer)
{
    $sql = "INSERT INTO ".db_prefix."personalfinances_transactions ";
    $sql.= "(date, amount, account, dst_account, paymode, flags, category, info, kxfer_id, user_id) VALUES ";
    $sql.= "('" . $date . "', '" . $amount . "', '" . $account_id . "', '" . $dst_account_id . "', '";
    $sql.=  $paymode . "', '" . $flags . "', '" . $category_id . "', '" . $info . "', '". $id_kxfer . "', '". user_id . "')";

    $ret = $db->exec($sql);
    return $db->lastInsertId();
}

function db_relateTransaction ($db, $transaction_id, $id_kxfer)
{
    $sql = "UPDATE ".db_prefix."personalfinances_transactions SET ";
    $sql.= "kxfer_id = " . $id_kxfer. " WHERE ";
    $sql.= "id = " . $transaction_id;

    $ret = $db->exec($sql);
}

/*
 * Helpers
 */
function julianDayNumberGetTimestampHelper ($julianDayNumber)
{
    $timezone = new DateTimeZone('UTC');
    $date = DateTime::createFromFormat('Y-m-d H:i:s', '1-1-0 00:00:00', $timezone);
    $date->add(new DateInterval('P' . $julianDayNumber . 'D'));
    $date->add(new DateInterval('PT12H'));

    return $date->getTimestamp();
}

function getKeyArrayHelper ($array, $key)
{
    $ret = 0;
    if (array_key_exists((string)$key, $array))
        $ret = $array[(string)$key];

    return $ret;
}

/*
 * Main code
 */
if (!file_exists($xmlfile))
     exit('Error abriendo test.xml.');

$db = new PDO("mysql:host=".db_host.";dbname=".db_name, db_user, db_pass);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$xml = simplexml_load_file($xmlfile);

// Fill banks..
foreach($xml->account as $account) {
    $bank_id = db_getBank ($db, $account["bankname"]);
    if (!$bank_id) {
        $bank_id = db_insertBank($db, $account["bankname"]);
    }
}

// Fill accounts.
$oldAccount = [];
foreach($xml->account as $account) {
    $account_id = db_getAccount ($db, $account["name"], $account["bankname"]);
    if (!$account_id) {
        $account_id = db_insertAccount($db, $account["name"], $account["bankname"], $account["type"], $account["initial"]);
    }
    $xml_id = $account["key"];
    $oldAccount[(string)$xml_id] = $account_id;
}

//  Fill categories.
$oldCat = [];
foreach($xml->cat as $cat) {
    $parent_id = getKeyArrayHelper($oldCat, $cat["parent"]);

    $cat_id = db_getCategory ($db, $cat["name"], $parent_id);
    if (!$cat_id) {
        $cat_id = db_insertCategory($db, $cat["name"], $parent_id);
    }
    $xml_id = $cat["key"];
    $oldCat[(string)$xml_id] = $cat_id;
}

// Fill transactions.
$incomeId = [];
$expenseId = [];
foreach($xml->ope as $ope) {
    $date = julianDayNumberGetTimestampHelper ($ope["date"]);
    $account_id = getKeyArrayHelper($oldAccount, $ope["account"]);
    $dst_account_id = getKeyArrayHelper($oldAccount, $ope["dst_account"]);
    $category_id = getKeyArrayHelper($oldCat, $ope["category"]);

    while (db_getTransactionByDate ($db, $date) > 0)
        $date+=60;

    $ope_id = db_insertTransaction ($db, $date, $ope["amount"], $account_id, $dst_account_id,
                                    $ope["paymode"], $ope["flag"], $category_id,
                                    $ope["info"], 0);
    if ($ope["kxfer"]) {
        if ($ope["amount"] > 0)
            $incomeId[(string)$ope["kxfer"]] = $ope_id;
        else
            $expenseId[(string)$ope["kxfer"]] = $ope_id;
    }
}

// Relate internal transactions.
foreach ($incomeId as $oldKxfer => $incomeOpeId) {
    $expenseOpeId = getKeyArrayHelper($expenseId, $oldKxfer);

    db_relateTransaction ($db, $incomeOpeId, $expenseOpeId);
    db_relateTransaction ($db, $expenseOpeId, $incomeOpeId);
}
