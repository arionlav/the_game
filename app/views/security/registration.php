<?php
use \config\App;

?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Sing up</title>
    <link rel="stylesheet" href="../css/style.css" type="text/css" />
    <script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
</head>
<body>
<h1>Sing up</h1>
<hr/>
<div class="form">
    <form id="frmLogin" action="<?=App::url(['security/registration', 'e'=>0]) ?>" method="post">
        <div>
            <label for="txtLogin">Login</label>
            <input id="txtLogin" type="text" name="login" class="formInputTextPriceChange" value='<?=$login ?>'/>
        </div>
        <div>
            <label for="txtPassword">Password</label>
            <input id="txtPassword" type="password" class="formInputTextPriceChange" name="password" />
        </div>
        <div>
            <label for="confirm">Confirm password</label>
            <input id="confirm" type="password" class="formInputTextPriceChange" name="confirm" />
        </div>
        <div id="buttonDiv">
            <input id="submit" type="submit" value="Sing up">
        </div>
    </form>
</div>

<div class="returnMessage"><div id="errorMessage"><?= $returnMsg ?></div>
    <a href="<?= App::url(['security/login', 'e' => 0]) ?>">Sing in</a></div>
</body>
</html>
