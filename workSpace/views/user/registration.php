<div class="jumbotron w400 alCenter">
    <h2>Registration</h2>
    <br/>
    <form action="/registration" method="post">
        <label for="email">Email: </label><span class="error">{_err}</span>
        <input type="text" name="email" id="email" class="form-control" value="<?php /*if (isset($_POST['email'])) echo htmlspecialchars(strip_tags($_POST['email'])); */?>dom1@gmail.com" /> <!--required-->
        <br/>
        <label for="password">Password: </label><span class="error">{_err}</span>
        <input type="password" name="password" id="password" class="form-control" /> <!--required-->
        <br/>
        <label for="repeat_password">Repeat password: </label><span class="error">{_err}</span>
        <input type="password" name="repeat_password" id="repeat_password" class="form-control" /> <!--required-->
        <br/>
        <input type="submit" value="Готово" name="submit"/>
        {CSRFProtection}
    </form>
</div>