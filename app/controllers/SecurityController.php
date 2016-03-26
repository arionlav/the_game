<?php
namespace app\controllers;

use core\Controller;
use app\models\ModelSecurity;
use config\App;

/**
 * Class SecurityController responsible for login user
 *
 * @package app\controllers
 */
class SecurityController extends Controller
{
    /**
     * @inheritdoc
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new ModelSecurity();
    }

    /**
     * Action on request security/login
     * Login page
     *
     * @param array $params from GET request
     */
    public function actionLogin($params)
    {
        if ($post = App::post()) {
            $host = $this->model->checkEnterLogin($post['login']);

            if ($host == '') {
                $user = $this->model->checkLogin($post);

                if ($user) {
                    $host = ['site/index'];

                    // set session variables if user is
                    $_SESSION['privileges'] = $user['role'];
                    $_SESSION['login']      = $post['login'];
                    $_SESSION['id']         = \hash('sha256', $user['salt']);
                    $_SESSION['whoThat']    = \hash('sha256',
                        $post['login'] . $_SERVER['HTTP_USER_AGENT'] . $_SESSION['id']);


                    $this->model->updateUsersList($user['login']);

                } else {
                    $host = ['security/login', 'e' => 2];
                }
            }

            App::redirect($host);
        }

        /** @var array $params */
        $this->view->render('login', [
            'params' => $params
        ]);
    }

    public function actionRegistration()
    {
        $returnMsg = '';

        if ($post = App::post()) {
            $returnMsg = $this->model->insertUser($post);
        }

        $this->view->render('registration', [
            'returnMsg' => $returnMsg
        ]);
    }
}
