<?php

namespace Manager;

class User
{

    /**
     * Возвращает пользователей старше заданного возраста.
     * @param int $ageFrom
     * @return array
     */
    public  static function getUsersByAgeFrom(int $ageFrom): array
    {
        return \Gateway\User::getByAgeFrom($ageFrom);
    }

    /**
     * Возвращает пользователей по списку имен.
     * @return array
     */
    public static function getUsersByName(array $names): array
    {
        $users = [];

        // Можно сделать одним запросом.
        foreach ($names as $name) {
            if($user = \Gateway\User::getByName($name))
            {
                $users[] = $user;
            }
        }

        return $users;
    }

    /**
     * Добавляет пользователей в базу данных.
     * @param $users
     * @return array
     */
    public static function addUsers($users): array
    {
        $ids = [];

        foreach ($users as $user) {
            if($userId = \Gateway\User::add($user['name'], $user['lastName'], $user['age'])) {
                $ids[] = $userId;
            }
        }

        return $ids;
    }
}
