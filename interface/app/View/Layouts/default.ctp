<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$cakeDescription = __('SNACK');
?>
<!DOCTYPE html>
<html>
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $cakeDescription ?>:
        <?php echo $title_for_layout; ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <?php
    echo $this->Html->meta('icon');
    echo $this->fetch('meta');

    echo $this->Html->css('bootstrap.min');
    echo $this->Html->css('mybootstrap');
    echo $this->Html->css('bootstrap-responsive.min');
    echo $this->Html->css('bootstrap-datetimepicker.min');
    echo $this->Html->css('doubleListsSelector');
    echo $this->Html->css('snack');
    echo $this->Html->css('jquery-ui-bootstrap/jquery-ui-1.9.2.custom');
    echo $this->Html->css('jquery-ui-bootstrap/jquery.ui.1.9.2.ie');
    echo $this->Html->css('flags.css');
    echo $this->Html->css('jquery.terminal.css');
    echo $this->Html->css('bootstrap-toggle-buttons.css');
    echo $this->fetch('css');

    echo $this->Html->script('snake');
?>
    <style>
        body {
            padding-top: 60px;
	}
    </style>

    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
    <?php echo $this->Html->script('html5-ie'); ?>
    <![endif]-->
</head>
<body>
<div class="navbar navbar-inverse navbar-fixed-top maintopmenu">
    <div class="navbar-inner">
        <div class="container">
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <a class="brand" href=""><?php echo $cakeDescription ?></a>

            <div class="nav-collapse collapse">
                <ul class="nav">
                    <?php
                    echo '<li class="' . $this->fetch('radius_active') . '">' .
                        $this->Html->link(
                            __('Radius'),
                            array(
                                'controller' => 'radusers',
                                'action' => 'index',
                            ),
                            array()
                        ) . '</li>';

                    if(AuthComponent::user('role') == 'root'){
                        echo '<li class="' . $this->fetch('term_active') . '">' .
                            $this->Html->link(
                                __('Terminal'),
                                array(
                                    'controller' => 'terminal',
                                    'action' => 'index',
                                ),
                                array()
                            ) .
                            '</li>';
                    }
                ?>
                </ul>
                <ul class="nav pull-right">

                <?php

                if($this->Session->read('Auth.User')){
                    echo '<li>' .
                        $this->Html->link(
                            __('Logout from %s', $this->Session->read('Auth.User.username')) .
                            ' <i class="icon-off icon-white"></i>',
                            array(
                                'controller' => 'radusers',
                                'action' => 'logout'
                            ),
                            array('escape' => false)
                        ) .
                        '</li>';
                }

                ?>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="container-fluid">
    <div class="row-fluid bhbody">

    <? echo $this->fetch('content'); ?>

    </div>
    <hr/>
    <footer>
        <p>
	    <a href="http://bh-consulting.net" class="copyright"><?php echo $this->Html->image('BHConsulting.png', array('class' => 'logobh', 'alt' => 'B.H. Consulting')) ?></a>
	    <p>
		<strong id="snackcopy">SNACK 1.0</strong> &mdash; <em>Secure Network Access Control for Kids</em><br />
		<strong><?php echo __('Authors:') ?></strong> Nicolas BOUGET, <a href="http://julien.guepin.fr">Julien GUÉPIN</a>, Marc PINHÈDE, <a href="http://julien.vaubourg.com">Julien VAUBOURG</a>.<br />
		<em>Copyright <a href="http://www.gnu.org/licenses/">GPL v3</a> &copy; 2013 <a href="http://bh-consulting.net">b.h. consulting</a></em>
	    </p>
            <!-- Flag sprites downloaded from http://flag-sprites.com/ (CC by-sa) -->
            <span class="flags">
                <? echo $this->Html->link($this->Html->image('blank.gif', array('class' => 'flag flag-fr', 'alt' => __('French'))), array('controller' => 'app', 'action' => 'changeLang', 'fra'), array('escape' => false)); ?>
                <? echo $this->Html->link($this->Html->image('blank.gif', array('class' => 'flag flag-us', 'alt' => __('English'))), array('controller' => 'app', 'action' => 'changeLang', 'eng'), array('escape' => false)); ?>
            </span>
        </p>
    </footer>
</div>
<?php
echo $this->element('sql_dump');
echo $this->Html->script('jquery.min');
echo $this->Html->script('jquery-ui.min');
echo $this->Html->script('bootstrap.min');
echo $this->Html->script('bootstrapify');
echo $this->Html->script('jquery.toggle.buttons');
echo $this->Html->script('bootstrap-datetimepicker.min');
echo $this->Html->script('doubleListsSelector');
echo $this->Html->script('backups');
echo $this->Html->script('checkboxRangeSelection');
echo $this->Html->script('jquery.bootstrap.wizard');
echo $this->Html->script('snack');
echo $this->fetch('script');
?>
</body>
</html>
