<!-- Forgot Password -->
<div class="modal fade bs-example-modal-lg in"  tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" id="ForgotPasswordPopup" aria-hidden="true">
    <div class="modal-dialog" id="forgotDialog">
        <div class="modal-content">
            <?= $this->Form->create(null, ['url' => ['action' => 'forgotPassword']]);?>
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

<!-- Login -->
<div class="row">
    <div class=" col-xs-10 col-xs-offset-1 col-sm-5 col-sm-offset-4 col-md-5 col-md-offset-4  col-lg-4 col-lg-offset-4">
        <div class="box">
            <?php echo $this->Form->create('User', array(
                'class' => 'form-signin'
            )); ?>
            <?php echo $this->Form->input('email', array(
                'placeholder'=> 'Email',
                'label' => FALSE,
                'class' => 'form-control',
                'div' => array(
                    'class' => 'form-group'
                )
            )); ?>

            <?php echo $this->Form->input('password', array(
                'placeholder'=> 'Password',
                'label' => FALSE,
                'class' => 'form-control',
                'div' => array(
                    'class' => 'form-group'
                )
            )); ?>
            <button class="btn btn-lg btn-primary btn-block" type="submit">
                <?=__("Login");?></button>

            <div class="row">
                <div class="col-sm-12 col-xs-12 col-md-12 ">
                    <a href="#" class="need-help" data-toggle="modal" data-target="#ForgotPasswordPopup"><?=__("Forgot Password");?>?</a>
                </div>

            </div>


                <a href="<?=$this->Url->build('/users/register', true);?>" class="btn btn-success pull-right">
                    <?=__("Sign Up for Free");?>
                </a>
            <div class="clearfix"></div>

            <?php echo $this->Form->end();?>
        </div>
        <br>


    </div>
</div>
<!-- Login -->
