<? 
$this->extend('/Common/radius_sidebar');
$this->assign('users_active', 'active');
?>
<h1>Cisco user <? echo $raduser['Raduser']['username']; ?></h1>

<p><strong>Comment: </strong>
<? echo $raduser['Raduser']['comment']; ?></p>

<? foreach($radchecks as $r){
    if($r['Radcheck']['attribute'] == 'NAS-Port-Type')
        echo '<p><strong>NAS Port Type: </strong> ' . $r['Radcheck']['value'];
    if($r['Radcheck']['attribute'] == 'Simultaneous-Use')
        echo '<p><strong>' . __('Simultaneous use:') . '</strong> ' . $r['Radcheck']['value'];
    if($r['Radcheck']['attribute'] == 'Expiration')
        echo '<p><strong>' . __('Expiration date:') . ' </strong> ' . $r['Radcheck']['value'];
}
?>
