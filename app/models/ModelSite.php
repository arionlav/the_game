<?php
namespace app\models;

use config\App;
use core\Model;

/**
 * Class ModelSite provide logic for index page and adding price list
 *
 * @package app\models
 */
class ModelSite extends Model
{
    /**
     * checks if something interesting happened
     *
     * @param string $lastCountUsers Last number of the users on the site
     * @return array Array for client
     */
    public function runCycle($lastCountUsers)
    {
        $db    = App::dbConnect();
        $limit = 3;
        $time  = time();

        while (time() - $time < $limit) {
            if (! $lastCountUsers) {
                // at first iteration
                $sql   = "SELECT id, name FROM users ORDER BY id";
                $stmt  = $db->query($sql);
                $users = $stmt->fetchAll(\PDO::FETCH_NUM);

                $sql     = "SELECT id, name, people, lobbyName, roundLength, date FROM lobby ORDER BY date";
                $stmt    = $db->query($sql);
                $lobbies = $stmt->fetchAll(\PDO::FETCH_NUM);

                $file[0][0] = $users;
                $file[0][1] = count($users);
                $file[1]    = $lobbies;

                return $file;
            }

            // call everytime with interval in sleep()
            $sql   = "SELECT id, name FROM users ORDER BY id";
            $stmt  = $db->query($sql);
            $users = $stmt->fetchAll(\PDO::FETCH_NUM);

            $sql     = "SELECT id, name, people, lobbyName, roundLength, date FROM lobby ORDER BY date";
            $stmt    = $db->query($sql);
            $lobbies = $stmt->fetchAll(\PDO::FETCH_NUM);

            if ($lastCountUsers != count($users)) {
                // if anybody login
                $file[0][0] = $users;
                $file[0][1] = count($users);
                $file[1]    = $lobbies;

                return $file;
            }
            // wait a some time
            sleep(2);
        }
    }

    /**
     * Update lobby, set 1 player for the room
     *
     * @param int $id Lobby id
     */
    public function lobbyBreak($id)
    {
        $db  = App::dbConnect();
        $sql = "UPDATE lobby SET people = 1, secondPlayer = '', readyFirst = 0, readySecond = 0 WHERE id = '{$id}'";
        $db->exec($sql);
    }

    /**
     * Player said that he is ready! Update lobby
     *
     * @param int $id Lobby id
     * @return int
     */
    public function playerReady($id)
    {
        $lobby = $this->getLobbyById($id);
        $db    = App::dbConnect();
        $sql   = '';

        if ($lobby[1] === $_SESSION['login']) {
            $sql = "UPDATE lobby SET readyFirst = 1 WHERE id = '{$id}'";
        } elseif ($lobby[3] === $_SESSION['login']) {
            $sql = "UPDATE lobby SET readySecond = 1 WHERE id = '{$id}'";
        } else {
            App::redirect(['site/error', 'e' => 'Error']);
        }

        return $db->exec($sql);
    }

    /**
     * Player bet. Update lobby
     *
     * @param int $id Lobby id
     * @param string $answer Player answer
     * @param $lot
     * @return int
     */
    public function makeChoice($id, $answer, $lot)
    {
        $db = App::dbConnect();

        $db->beginTransaction();
        $lobby = $this->getLobbyById($id);
        $game  = unserialize($lobby[6]);


        if ($lobby[1] === $_SESSION['login']) {

            $game[$lot][1] = $answer;
            if (isset($game[$lot][2])) {
                $game[$lot][3] = $this->checkAnswer($game[$lot][1], $game[$lot][2]);
            }

        } elseif ($lobby[3] === $_SESSION['login']) {

            $game[$lot][2] = $answer;
            if (isset($game[$lot][1])) {
                $game[$lot][3] = $this->checkAnswer($game[$lot][1], $game[$lot][2]);
            }

        } else {
            App::redirect(['site/error', 'e' => 'Error']);
        }

        $game = serialize($game);

        $sql = "UPDATE lobby SET game = '$game' WHERE id = '{$id}'";

        if ($db->exec($sql)) {
            $db->commit();

            return 1;
        } else {
            $db->rollBack();

            return 0;
        }
    }

    /**
     * Delete lobby
     *
     * @param int $id Lobby id
     */
    public function deleteLobby($id)
    {
        $db  = App::dbConnect();
        $sql = "DELETE FROM lobby WHERE id = '{$id}'";
        $db->exec($sql);
    }

    /**
     * Add new lobby
     *
     * @param array $post Incoming values
     * @return int
     */
    public function addLobby($post)
    {
        $db = App::dbConnect();
        $id = rand(1000, 9999);

        if ($post['lobbyName']) {
            $lobbyName = $post['lobbyName'];
            if (strlen($lobbyName) > 32) {
                $lobbyName = substr($lobbyName, 0, 32);
            }
        } else {
            $lobbyName = $id;
        }

        if (strlen($post['roundLength']) == 2 && $post['roundLength'] * 1) {
            $roundLength = $post['roundLength'];
        } else {
            $roundLength = 10;
        }

        $sql  = "INSERT INTO lobby (id, name, people, lobbyName, roundLength)
                VALUES ($id, '{$_SESSION['login']}', 1, :lobbyName, :roundLength)";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':lobbyName', $lobbyName);
        $stmt->bindValue(':roundLength', $roundLength);
        $stmt->execute();

        return $id;
    }

    /**
     * Player enter in the room. Who are you?
     *
     * @param int $id Lobby id
     * @return bool
     */
    public function game($id)
    {
        $lobby = $this->getLobbyById($id);

        if ($lobby[1] !== $_SESSION['login']) {
            if ($lobby[2] == 2 && $lobby[3] !== $_SESSION['login']) {
                return false;
            }

            if ($lobby[1] !== $_SESSION['login']) {
                $this->incrementPeople($id);
            }
        }

        return true;
    }

    /**
     * Take lobby for client
     *
     * @param int $id Lobby id
     * @return mixed
     */
    public function tekeLobby($id)
    {
        $file[0] = $this->getLobbyById($id);

        if (! $file[0]) {
            echo 0;
            exit;
        }

        $file[0][6] = unserialize($file[0][6]);

        return $file;
    }

    /**
     * Get lobby by id
     *
     * @param int $id Lobby id
     * @return array|false
     */
    public function getLobbyById($id)
    {
        $db = App::dbConnect();

        $sql   = "SELECT id, name, people, secondPlayer, readyFirst, readySecond, game, lobbyName, roundLength, date
                  FROM lobby WHERE id = '{$id}'";
        $stmt  = $db->query($sql);
        $lobby = $stmt->fetch(\PDO::FETCH_NUM);

        return $lobby;
    }

    /**
     * Check answers
     *
     * @param string $one First player answer
     * @param string $two Second player answer
     * @return int|null
     */
    public function checkAnswer($one, $two)
    {
        $res = null;

        if ($one == 'empty' && $two == 'empty') {
            $res = 0;
        } elseif ($two == 'empty') {
            $res = 1;
        } elseif ($one == 'empty') {
            $res = 2;
        } else {
            switch ($one) {
                case 'rock':
                    switch ($two) {
                        case 'rock':
                            $res = 0;
                            break;
                        case 'scissors':
                            $res = 1;
                            break;
                        case 'paper':
                            $res = 2;
                            break;
                    };
                    break;
                case 'scissors':
                    switch ($two) {
                        case 'rock':
                            $res = 2;
                            break;
                        case 'scissors':
                            $res = 0;
                            break;
                        case 'paper':
                            $res = 1;
                            break;
                    };
                    break;
                case 'paper':
                    switch ($two) {
                        case 'rock':
                            $res = 1;
                            break;
                        case 'scissors':
                            $res = 2;
                            break;
                        case 'paper':
                            $res = 0;
                            break;
                    };
                    break;
                default:
                    $res = null;
            }
        }

        return $res;
    }

    /**
     * Set people = 2, when player enter in the room
     *
     * @param int $id Lobby id
     */
    public function incrementPeople($id)
    {
        $db  = App::dbConnect();
        $sql = "UPDATE lobby SET people = 2, secondPlayer = '{$_SESSION['login']}' WHERE id = '{$id}'";
        $db->exec($sql);
    }
}
