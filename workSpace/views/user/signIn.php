<?php if (controllerManager::$variables['registerComplete']) : ?>
<div class="panel panel-info" style="width: 379px; margin: 0 auto;">
    <div class="panel-heading">
        <h3 class="panel-title">Registration complete</h3>
    </div>
    <div class="panel-body">
        Congratulation! Your account have been created successful. In your email was sent message which contains confirmation link.<br/>
        Check your email in order to pass that link.
    </div>
</div><br/>
<?php endif; ?>
<div class="panel panel-primary lgPanel">
    <div class="panel-heading">
        <h3 class="panel-title">Sign In</h3>
    </div>
    <div class="panel-body pd32">
        <form action="/sign_in" method="post">
            <label for="email">Email: </label><span class="error">{_err}</span>
            <input type="text" name="email" id="email" class="form-control w330" value="{typedEmail}" maxlength="50" required />
            <br/>
            <label for="password">Password: </label><span class="error">{_err}</span>
            <input type="password" name="password" id="password" class="form-control w330" required />
            <br/>
            <input type="submit" value="Sign In" class="btn btn-lg btn-primary"/>
            <div style="position: absolute; width: 230px; height: auto; margin: -45px 0 0 210px">
                <label for="rememberMe">Remember me</label>
                <input style="margin-left: 10px; position: absolute; margin-top: 6px;" type="checkbox" id="rememberMe" value="1"<?php if (isset($_POST['rememberMe'])) echo ' checked="checked";'?> name="rememberMe" />
            </div>
            <div style="position: absolute; width: 230px; height: auto; margin: -20px 0 0 92px">
                <span class="error">{errorMessage}</span>
            </div>
            {CSRFProtection}
        </form>
    </div>
</div>