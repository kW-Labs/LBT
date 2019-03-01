<div id="welcome_banner">
    <div class="row">
        <div class="text-center">
            <img src="/img/benchmarking_text.png" alt="Welcome to the Laboratory Benchmarking Tool!"
                 class="benchmarking-text">
        </div>
        <p class="lead mt-2 text-center">
            Use the LBT to compare the energy use of your lab buildings with that of similar facilities in the US. The
            tool's peer-group database contains owner-submitted data from an ever-growing number of lab facilities.
            <br><br>
            Current total: <b id="number-of-buildings-text"> <?= $number_of_buildings_in_bpd; ?> </b> peer-group facilities
            <br>
        </p>
    </div>

    <?PHP
    if (!$loggedIn):
        ?>
        <div class="row">
            <div class="col-sm-3 col-sm-offset-3">
                <a href="<?= $this->Url->build(["controller" => "users", "action" => "login"]); ?>"
                   class="btn btn-block btn-lg btn-danger mr-3">Login</a>
                <a href="<?= $this->Url->build(["controller" => "users", "action" => "register"]); ?>"
                   class="white-link">Don't have an account? Sign up!</a>
            </div>
            <div class="col-sm-3 ">
                <a href="<?= $this->Url->build(["controller" => "buildings", "action" => "charts"]); ?>"
                   class="btn btn-block btn-lg btn-danger">View data as guest</a>
            </div>
            
    
        </div>
        <div class="row">
            <div class="col-sm-12 mt-3 text-center">
                
                <p><strong>EXISTING LABS21 BENCHMARKING TOOL USERS:</strong> Your account has been migrated to the LBT! Your new username is the email address you used for the Labs21 site. Click on Login and use the Forgot Password? link to choose a new password and get started!</p>
            </div>
        </div>
    <?PHP
    else:
        ?>
        <div class="row">
            <p class="mt-3">
                You are logged in as
                <?PHP
                echo $this->request->session()->read('Auth.User')['email'] . '.';
                ?>
            </p>
        </div>
    <?PHP
    endif;
    ?>
</div>
