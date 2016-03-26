<?php
namespace app\models;

use config\App;
use core\helpers\GenerateException;
use core\helpers\Query;

/**
 * Class ModelSecurity provide logic for user login
 *
 * @package app\models
 */
class ModelSecurity
{
    /**
     * Check incoming login and password
     *
     * @param array $post Input values
     * @return array|bool
     */
    public function checkLogin(array $post)
    {
        $user = static::getUser($post['login']);

        if (! empty($user)) {
            $passwordHash = static::getHash($post['password'], $user['salt'], $user['iterationCount']);

            ($passwordHash == $user['password'])
                ? $result = $user // "please, welcome!"
                : $result = false; // password is wrong
        } else {
            $result = false;
        }

        return $result;
    }

    /**
     * Get user by login
     *
     * @param string $login Username
     * @return array
     */
    public static function getUser($login)
    {
        $query = new Query();

        $sth = $query
            ->select([
                'id',
                'login',
                'password',
                'salt',
                'iterationCount',
                'role',
                'graph_type'
            ])
            ->from('user_users')
            ->whereBindStmt([
                'login' => ':login'
            ])
            ->prepareBindStatement();

        $sth->bindParam(':login', $login);

        return $query->executeBindStmtOne($sth);
    }

    /**
     * Create password hash
     *
     * @param string  $password       Original password
     * @param string  $salt           Salt
     * @param integer $iterationCount The number of iterations
     * @return string Password hash
     */
    public static function getHash($password, $salt, $iterationCount)
    {
        $passwordHash = '';

        if ($iterationCount and $salt != '') {
            for ($i = 0; $i < $iterationCount; $i++) {
                $passwordHash = \hash('sha256', $password . $salt);
            }
        } else {
            GenerateException::getException('Hash password does not create, wrong input value', __CLASS__, __LINE__);
        }

        return $passwordHash;
    }

    /**
     * Check incoming login
     *
     * @param string $login Incoming login
     * @return array|null
     */
    public function checkEnterLogin($login)
    {
        $host = null;

        if (! preg_match("/^[a-zA-Z0-9]+$/", $login)) {
            $host = ['security/login', 'e' => 3];
        } elseif (strlen($login) < 3 or strlen($login) > 30) {
            $host = ['security/login', 'e' => 4];
        }

        return $host;
    }

    public function updateUsersList($login)
    {
        $db  = App::dbConnect();
        $sql = "INSERT INTO users (name, lastActivity) VALUES ('$login', null)";
        $db->exec($sql);

        /*$lastId = $db->lastInsertId('id');
        var_dump($lastId);
        exit;*/
    }

    public function insertUser($post)
    {
        if ($post['login'] and $post['password'] and $post['confirm']) {
            $checkLogin = $this->checkLoginRegistration($post['login']);
        } else {
            $returnMsg = 'Enter login, password and confirm password';

            return $returnMsg;
        }

        if ($post['password'] !== $post['confirm']) {
            $returnMsg = 'Passwords do not match';

            return $returnMsg;
        }

        if ($checkLogin === true) {
            $resultInsert = $this->insertNewUser($post['login'], $post['password']);

            if ($resultInsert === false) {
                GenerateException::getException('Insert new user impossible, check values.', __CLASS__, __LINE__);
            }

            $returnMsg = "Success! Use your login and password for login";
        } else {
            $returnMsg = 'Login already exists. Enter other login';
        }

        return $returnMsg;
    }

    /**
     * Check incoming username to originality
     *
     * @param string $login Incoming username
     * @return bool
     */
    private function checkLoginRegistration($login)
    {
        $loginsFromDb = $this->getAllLogin();

        $result = true;

        foreach ($loginsFromDb as $l) {
            if ($l['login'] == $login) {
                $result = false;
                break;
            }
        }

        return $result;
    }

    /**
     * Get all username
     *
     * @return array All username from database
     * @throws GenerateException
     */
    public function getAllLogin()
    {
        $query = new Query();

        $loginAll = $query
            ->select([
                'login'
            ])
            ->from('user_users')
            ->all();

        if (empty($loginAll)) {
            GenerateException::getException('Any loginAll does not found, check SQL syntax', __CLASS__, __LINE__);
        }

        return $loginAll;
    }

    /**
     * Insert new user
     *
     * @param string $login    Incoming login
     * @param string $password Incoming password
     * @return true|GenerateException
     */
    private function insertNewUser($login, $password)
    {
        $iCount = rand(40, 100);
        $salt   = uniqid(mt_rand(), true);

        $passwordHash = self::getHash($password, $salt, $iCount);

        $query = new Query();

        $result = $query
            ->insertInto('user_users', [
                'login'          => ':login',
                'password'       => ':password',
                'salt'           => ':salt',
                'iterationCount' => ':iterationCount',
                'role'           => ':role'
            ])
            ->prepareBindStatement()
            ->execute([
                'login'          => $login,
                'password'       => $passwordHash,
                'salt'           => $salt,
                'iterationCount' => $iCount,
                'role'           => 2,
            ]);

        return $result;
    }
}
