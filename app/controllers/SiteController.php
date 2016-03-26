<?php
namespace app\controllers;

use app\models\ModelGeneral;
use app\models\ModelSite;
use config\App;
use core\Controller;

/**
 * Class SiteController responsible of index page and adding price list
 *
 * @package app\controllers
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    function __construct()
    {
        parent::__construct();
        $this->model = new ModelSite();
    }

    /**
     * Action on request site/index
     * Main page on site
     */
    public function actionIndex()
    {
        $this->view->render('index');
    }

    /**
     * Comet for index page
     */
    public function actionResult()
    {
        $lastCountUsers = $_GET['lasCountUsers'];

        $result = $this->model->runCycle($lastCountUsers);

        echo json_encode($result);
    }

    /**
     * Add lobby
     */
    public function actionAddLobby()
    {
        $post = App::post();

        $id = $this->model->addLobby($post);

        App::redirect(['site/game', 'id' => $id]);
    }


    /**
     * The game page
     *
     * @param array $get Incoming values
     */
    public function actionGame($get)
    {
        $id = trim(strip_tags($get['id']));

        if ($this->model->game($id) === false) {
            App::redirect(['site/error', 'e' => 'Room full']);
        }

        $this->view->render('game', [
            'id' => $id
        ]);
    }

    /**
     * Take lobby and return to client
     *
     * @param array $get Incoming values
     */
    public function actionTakeLobby($get)
    {
        $id = trim(strip_tags($get['id']));

        $result = $this->model->tekeLobby($id);

        echo json_encode($result);
    }

    /**
     * User out from the room
     *
     * @param array $get Incoming values
     */
    public function actionBreak($get)
    {
        $id = trim(strip_tags($get['id']));

        $this->model->lobbyBreak($id);

        App::redirect(['site/index']);
    }

    /**
     * Update lobby if player are ready
     *
     * @param array $get Incoming values
     */
    public function actionPlayerReady($get)
    {
        $id = trim(strip_tags($get['id']));

        $result = $this->model->playerReady($id);

        if ($result) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * Player bet
     *
     * @param array $get Incoming values
     */
    public function actionMakeChoice($get)
    {
        $id     = trim(strip_tags($get['id']));
        $answer = trim(strip_tags($get['val']));
        $lot    = trim(strip_tags($get['lot']));

        $result = $this->model->makeChoice($id, $answer, $lot);

        if ($result) {
            echo 1;
        } else {
            echo 0;
        }
    }

    /**
     * Close lobby
     *
     * @param array $get Incoming values
     */
    public function actionDeleteLobby($get)
    {
        $id = trim(strip_tags($get['id']));

        $this->model->deleteLobby($id);
    }

    /**
     * Show some error for users
     *
     * @param array $params from GET request
     */
    public function actionError($params)
    {
        $this->view->render('error', [
            'errorMsg' => $params['e']
        ]);
    }
}
