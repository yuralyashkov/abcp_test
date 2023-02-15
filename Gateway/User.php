<?php

namespace Gateway;

use PDO;

class User
{

    const limit = 10;

    /**
     * @var PDO
     */
    public static $instance;

    /**
     * Реализация singleton
     * @return PDO
     */
    public static function getInstance(): PDO
    {
        if (is_null(self::$instance)) {
            $dsn = 'mysql:dbname=test1;host=localhost';
            $user = 'root';
            $password = '';
            self::$instance = new PDO($dsn, $user, $password);
        }

        return self::$instance;
    }

    /**
     * Возвращает список пользователей старше заданного возраста.
     * @param int $ageFrom
     * @return array
     */
    public static function getByAgeFrom(int $ageFrom): array
    {
        $stmt = self::getInstance()->prepare("SELECT `id`, `name`, `lastName`, `from`, `age`, `settings` FROM `Users` WHERE `age` > :ageFrom LIMIT " . self::limit);
        $stmt->execute(['ageFrom' => $ageFrom]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $users = [];
        foreach ($rows as $row) {
              $users[] = self::getUserFields($row);
        }

        return $users;
    }

    /**
     * Возвращает пользователя по имени.
     * @param string $name
     * @return array|bool
     */
    public static function getByName(string $name)
    {

        $stmt = self::getInstance()->prepare("SELECT `id`, `name`, `lastName`, `from`, `age`, `settings` FROM `Users` WHERE `name` = :name");
        $stmt->execute(['name' => $name]);
        $user_by_name = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user_by_name) {
            return self::getUserFields($user_by_name);
        } else return false;
    }

    /**
     * Добавляет пользователя в базу данных.
     * @param string $name
     * @param string $lastName
     * @param int $age
     * @return string
     */
    public static function add(string $name, string $lastName, int $age): string
    {
        $sth = self::getInstance()->prepare("INSERT INTO `Users` (`name`, `lastName`, `age`) VALUES (:name, :lastName, :age)");
        $sth->execute([':name' => $name, ':age' => $age, ':lastName' => $lastName]);

        return self::getInstance()->lastInsertId();
    }


    /**
     * Формирует безопасный массив свойств пользователя
     * @param array $user
     * @return array
     */
    public static function getUserFields(array $user): array
    {
        $key = $user['settings'] ? json_decode($user['settings'])['key']: '';
        return [
            'id' => $user['id'],
            'name' => $user['name'],
            'lastName' => $user['lastName'],
            'from' => $user['from'],
            'age' => $user['age'],
            'key' => $key
        ];
    }
}
