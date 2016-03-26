<?
use config\App;
use core\Route;
use core\SessionManager;
SessionManager::checkAccess();

if (
    strpos($_SERVER{'REQUEST_URI'}, 'security/login') !== false ||
    strpos($_SERVER{'REQUEST_URI'}, 'security/registration') !== false
) {
    SessionManager::destroySession();
}

(is_null($otherViewRoot))
    ? $pathToView = strtolower (Route::$controllerName)
    : $pathToView = $otherViewRoot;

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <title><?=$this->getTitle($content) ?></title>
    <link rel="stylesheet" href="<?=App::$pathToRoot ?>/css/style.css" type="text/css" />
    <script type="text/javascript" src="<?=App::$pathToRoot ?>/js/jquery-1.11.2.min.js"></script>
</head>
<body>

    <div class="navigationTop">
        <a href="<?=App::url(['site/index']) ?>">Index page</a> <span>|</span>
        <a href="<?=App::url(['security/login', 'e' => 0]) ?>">Logout</a>
    </div>
    <hr/>

<? require_once 'app/views/' . $pathToView . '/' . $content . '.php'; ?>
</body>
</html>