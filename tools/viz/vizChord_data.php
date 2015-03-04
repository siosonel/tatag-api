<?php
error_reporting(E_ALL ^ E_NOTICE);
header('content-type: text/plain');
chdir("../../");

require_once "config.php";
require_once "models/Utils.php";

DBquery::init($dbs, array("tatagsim"));


$tickNum = isset($_GET['tickNum']) ? $_GET['tickNum'] : 1;

$sql = "SELECT f.brand_id as source, t.brand_id as target, SUM(amount) as amount
FROM records r
JOIN accounts f ON r.from_acct=f.account_id
JOIN accounts t ON r.to_acct=t.account_id
WHERE ref_id=?
GROUP BY f.brand_id, t.brand_id";

$rows = DBquery::get($sql, array($tickNum));
if (!$rows) exit('[]');

foreach($rows AS $r) {
	$target["b".$r['target']]['amount'] += $r['amount'];
	$target["b".$r['target']]['imports'][] = "b".$r['source'];
}

foreach($target AS $k=>$v) $data[] = array(
	"name"=>$k,
	"size"=>1, //1*substr("".$v['amount'],0,1),
	"imports"=>$v['imports']
);

exit(json_encode($data));

