<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
<h1>Certificate user <? echo h($raduser['Raduser']['username']); ?></h1>

<p><strong>Comment: </strong>
<? echo $raduser['Raduser']['comment']; ?></p>

<p><strong>Certificate path: </strong><? echo $raduser['Raduser']['cert_path']; ?></p>

<? foreach($radchecks as $r){
    if($r['Radcheck']['attribute'] == 'EAP-Type')
        echo '<p><strong>EAP Type: </strong> ' . $r['Radcheck']['value'];
    if($r['Radcheck']['attribute'] == 'Simultaneous-Use')
        echo '<p><strong>Simultaneous use: </strong> ' . $r['Radcheck']['value'];
    if($r['Radcheck']['attribute'] == 'Expiration')
        echo '<p><strong>Expiration date: </strong> ' . $r['Radcheck']['value'];
}
?>
