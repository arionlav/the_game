<?php
namespace config;

use core\helpers\GenerateException;

/**
 * Class App contain application params
 *
 * @package config
 */
class App
{
    /**
     * @static
     * @var Config Instance of application
     */
    public static $app;

    public static $db;

    /**
     * @static
     * @var string Path to root folder
     */
    public static $pathToRoot = '/the_game';

    /**
     * @static
     * @var string Path for loading price lists
     */
    public static $pathToLoadFiles = 'uploads';

    /**
     * @static
     * @var string Namespace for controllers
     */
    public static $controllersNamespace = '\app\controllers\\';

    /**
     * @static
     * @var string Count rows on the search result page
     */
    public static $rowOnSearchPage = 500;

    /**
     * @static
     * @var string Default tag title
     */
    public static $defaultTitle = 'Обработчик прайсов';

    /**
     * @var string Mysql User name
     */
    const DB_USER = 'root';

    /**
     * @var string Mysql Password
     */
    const DB_PASS = '';

    /**
     * @var string Mysql Host
     */
    const DB_HOST = 'localhost';

    /**
     * @var string MySQL db name
     */
    const DB_DBNAME = 'the_game';

    /**
     * Create URL.
     * Example: from App::url(['security/login', 'param' => 2, 'id' => 11], 'content')
     * URL: {self::$pathToRoot}/security/login?param=2&id=11#content
     *
     * @static
     * @param array       $urlEnter Route params
     * @param null|string $hash     If need add hash to link
     * @return string URL
     */
    public static function url(array $urlEnter, $hash = null)
    {
        $urlFinal = '';

        $i = 0;
        foreach ($urlEnter as $uKey => $uVal) {
            if ($uKey) {
                if ($i == 0) {
                    $urlFinal .= '?' . $uKey . '=' . $uVal;
                    $i++;
                } else {
                    $urlFinal .= '&' . $uKey . '=' . $uVal;
                }
            } else {
                $url      = explode('/', $urlEnter[0]);
                $urlFinal = self::$pathToRoot . '/' . $url[0] . '/' . $url[1];
            }
        }

        if (! is_null($hash)) {
            $urlFinal .= '#' . $hash;
        }

        return $urlFinal;
    }


    /**
     * Redirect on the page $host
     * Example: From App::redirect(['security/login', 'param' => 2, 'id' => 11]);
     * Redirect to: {self::$pathToRoot}/security/login?param=2&id=11
     *
     * @param array $host Route params
     */
    public static function redirect(array $host)
    {
        $url = static::url($host);
        header('Location:' . $url);
        exit;
    }

    /**
     * Get $_POST array
     *
     * @static
     * @return false|array $_POST
     * @throws GenerateException
     */
    public static function post()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (empty($_POST)) {
                GenerateException::getException('Method is post, but Post array are empty. Something wrong.');
            }

            return $_POST;
        } else {
            return false;
        }
    }

    public static function dbConnect () {
        if (is_null(self::$db)) {
            $dns = 'mysql:host=' . App::DB_HOST . ';dbname=' . App::DB_DBNAME;
            self::$db = new \PDO($dns, App::DB_USER, App::DB_PASS);

            if (! self::$db) {
                throw new \PDOException('Connection failed');
            }

            return self::$db;
        }

        return self::$db;
    }
}
