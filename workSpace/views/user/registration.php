<div class="jumbotron w400 alCenter">
    <h2>Registration</h2>
    <br/>
    <form action="/registration" method="post">
        <label for="email">Email: </label>
        <input type="email" name="email" id="email" class="form-control" value="mail@gmail.cm" required />
        <br/>
        <label for="password">Password: </label>
        <input type="password" name="password" id="password" class="form-control" value="12" required />
        <br/>
        <label for="repeat_password">Repeat password: </label>
        <input type="password" name="repeat_password" id="repeat_password" value="12" class="form-control" required />
        <br/>
        <input type="submit" value="Готово" name="submit"/>
        {CSRFProtection}
    </form>
</div>