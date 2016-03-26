<?php
use \config\App;

switch ($params['e']) {
    case 1: $errorMsg   = 'You do not have permission to view this page. <br/>Pleace login'; break;
    case 2: $errorMsg   = 'Wrong login or password'; break;
    case 3: $errorMsg   = 'Use only latin symbols and numbers'; break;
    case 4: $errorMsg   = 'Login must be a min of 3 and a max of 30 characters'; break;
    case 5: $errorMsg   = 'Session expired, please login again'; break;
    case 6: $errorMsg   = 'A system error occurred'; break;
    default: $errorMsg  = '';
}
?>
<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>The Game</title>
    <link rel="stylesheet" href="../css/style.css" type="text/css" />
    <script type="text/javascript" src="js/jquery-1.11.2.min.js"></script>
</head>
<body>
<h1>Sing in</h1>
<hr/>
<div class="form">
    <form id="frmLogin" action="<?=App::url(['security/login', 'e'=>0]) ?>" method="post">
        <div>
            <label for="txtLogin">Login</label>
            <input id="txtLogin" type="text" name="login" class="formInputTextPriceChange" value='<?=$login ?>'/>
        </div>
        <div>
            <label for="txtPassword">Password</label>
            <input id="txtPassword" type="password" class="formInputTextPriceChange" name="password" />
        </div>
        <div id="buttonDiv">
            <input id="submit" type="submit" value="Sing in">
        </div>
    </form>

    <a href="<?= App::url(['security/registration']) ?>">Sing up</a>
</div>

<div class="returnMessage"><div id="errorMessage"><?=$errorMsg ?></div></div>
</body>
</html>
