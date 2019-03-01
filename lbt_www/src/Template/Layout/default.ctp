<?php
/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

$cakeDescription = 'LBT';
$email = $this->request->session()->read('Auth.User')['email'];

$myController = strtolower($this->request->params['controller']);
$myAction = strtolower($this->request->params['action']);

$buildingsActive = $myController == 'buildings' && $myAction == 'home' ? ' active' : '';
$myBuildingsActive = $myController == 'buildings' && ($myAction == 'index' || $myAction == 'add' || $myAction == 'edit') ? ' active' : '';
$chartsActive = $myController == 'buildings' && $myAction == 'charts' ? ' active' : '';
$aboutActive = $myController == 'help' && $myAction == 'about' ? ' active' : '';
$faqActive = $myController == 'help' && $myAction == 'faq' ? ' active' : '';

if ($myAction == 'home'){
    $string1 = 'mb-0';
    $string2 = 'mt-0';
}else{
    $string1 = '';
    $string2 = 'mt-0 ml-1';
}

?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#044a6e">
    <meta name="msapplication-TileColor" content="#ff0000">
    <meta name="theme-color" content="#ffffff">

    <title>
        <?= $cakeDescription ?>:
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>
    <?= $this->Html->css('bootstrap.min.css') ?>
    <?= $this->Html->css('custom.css') ?>
    <?= $this->Html->css('pnotify.custom.min.css') ?>
    <?= $this->Html->css('material.css') ?>
    <?= $this->Html->css('nouislider.min.css') ?>

    <?= $this->Html->script('jquery.min.js') ?>
    <?= $this->Html->script('bootstrap.min.js') ?>
    <?= $this->Html->script('pnotify.custom.min.js') ?>

    <!-- Echarts -->
    <?= $this->Html->script('popper.min.js') ?>
    <?= $this->Html->script('echarts.min.js') ?>
    <?= $this->Html->script('wNumb.js') ?>
    <?= $this->Html->script('nouislider.min.js') ?>
    <?= $this->Html->script('typeahead.bundle.min.js');?>


</head>
<body>
<!-- Static navbar -->
<nav class="navbar navbar-inverse navbar-static-top <?=$string1;?>">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li class="<?=$buildingsActive;?>"><a href="<?= $this->Url->build(["controller" => "buildings", "action" => "home"]); ?>"><?=__("Home");?></a></li>
                <?php if(isset($email)):?>
                    <li class="<?=$myBuildingsActive;?>"><a href="<?= $this->Url->build(["controller" => "buildings", "action" => "index"]); ?>"><?=__("Your Buildings");?></a></li>
                <?php endif;?>
                <li class="<?=$chartsActive;?>"><a href="<?= $this->Url->build(["controller" => "buildings", "action" => "charts"]); ?>"><?=__("Benchmark Analysis");?></a></li>
                <li class="<?=$faqActive;?>"><a href="<?= $this->Url->build(["controller" => "help", "action" => "faq"]); ?>"><?=__("FAQ");?></a></li>
                <li class="<?=$aboutActive;?>"><a href="<?= $this->Url->build(["controller" => "help", "action" => "about"]); ?>"><?=__("About");?></a></li>
                <li><a href="https://www.i2sl.org" target="_blank">I<sup>2</sup>SL</a></li>

            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <?php if(isset($email)):?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true"><?=$email;?> <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="<?= $this->Url->build('/users/profile', true) ?>" class="dropdown-item">
                                <i class="glyphicon glyphicon-cog"></i>
                                <span class="hidden-sm text"><?=__('My Account');?></span>
                            </a>

                        </li>
                        <li>
                            <a href="<?= $this->Url->build('/users/logout', true) ?>" id="logoutBtn" class="dropdown-item">
                                <i class="glyphicon glyphicon-off"></i>
                                <span class="hidden-sm text"><?=__('Logout - {0}', [$email]);?></span>
                            </a>
                        </li>
                    </ul>
                    <?php else:?>
                        <a href="<?= $this->Url->build(["controller" => "users", "action" => "login"]); ?>" class="dropdown-toggle" data-toggle="dropdown">Log In <span class="caret"></span></a>
                        <ul class="dropdown-menu dropdown-lr animated slideInRight" role="menu">
                            <div class="col-lg-12">
                                <?php echo $this->Form->create(null, [
                                    'url' => ['controller' => 'users', 'action' => 'login']
                                ]); ?>
                                    <div class="form-group">
                                        <label for="email">Username</label>
                                        <input type="text" name="email" id="email" tabindex="1" class="form-control" placeholder="Email" value="" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <label for="password">Password</label>
                                        <input type="password" name="password" id="password" tabindex="2" class="form-control" placeholder="Password" autocomplete="off">
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-xs-12 pull-right">
                                                <input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-success" value="Log In">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="text-center">
                                                    <a href="#" class="need-help" data-toggle="modal" data-target="#ForgotPasswordPopup"><?=__("Forgot Password");?>?</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" class="hide" name="token" id="token" value="a465a2791ae0bae853cf4bf485dbe1b6">
                                <?php echo $this->Form->end();?>
                            </div>
                        </ul>
                    <?php endif;?>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</nav>

<div class="container <?=$string2;?>">
    <?php echo $this->fetch('content'); ?>
</div>

<!-- Forgot Password -->
<div class="modal fade bs-example-modal-lg in"  tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="ForgotPasswordPopup" aria-hidden="true">
    <div class="modal-dialog" id="forgotDialog">
        <div class="modal-content">
            <?= $this->Form->create(null, ['url' => ['controller' => 'users','action' => 'forgotPassword']]);?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?=__("Close");?></span></button>
                <h4 class="modal-title" id="mySmallModalLabel"><?=__("Forgot Password");?></h4>
            </div>
            <div class="modal-body">
                <blockquote class="info bquote">
                    <h4><?=__("Please enter your email address.");?></h4>
                    <?=__("You will receive a link to create a new password via email. If you don't see an email from lbt@i2sl.org within a few minutes, please check your junk mail folder.");?>
                </blockquote>

                <input type="email" name="email" class="form-control" placeholder="<?=__("Email address");?>" required autofocus>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary pull-right" type="submit"><?=__("Get New Password");?></button>
            </div>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
<!-- Forgot Password -->

<footer class="footer">
    <div class="container text-center">
        <a href="http://www.lbl.gov" target="_blank" title="Lawrence Berkeley National Laboratory (Berkeley Lab)"><img src="/img/lbl_logo.png" width="366" height="60" alt="LBL Logo" class="mr-3 mt-2"></a>
        <a href="http://www.i2sl.org" target="_blank" title="International Institute for Substainable Laboratories"><img src="/img/i2sl_logo.png" width="310" height="60" alt="I2SL Logo" class="mr-3 mt-2"></a>
        <p class="text-muted">&copy; LBT <?=date('Y');?> <?=__("All rights reserved.");?> <a href="<?= $this->Url->build('/help/privacy', true) ?>"><?=__("Privacy");?></a></p>
    </div>
</footer>

<?= $this->Flash->render() ?>

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?=env('GOOGLE_ID');?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', '<?=env('GOOGLE_ID');?>');
</script>

</body>
</html>
