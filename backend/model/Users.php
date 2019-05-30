<?php

class Users extends Model {

    function register(array $data) {
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, surname, login, email, password, gender, registered) VALUES (:n, :s, :l, :e, :p, :g, NOW())');
        $p = password_hash($data['password'],
            PASSWORD_DEFAULT);
        $stmt->bindValue(':n',
            $data['name']);
        $stmt->bindValue(':s',
            $data['surname']);
        $stmt->bindValue(':l',
            $data['login']);
        $stmt->bindValue(':e',
            $data['email']);
        $stmt->bindValue(':g',
            $data['gender']);
        $stmt->bindValue(':p', $p);
        return $stmt->execute();
    }

    function getByLogin($login) {
        $stmt = $this->db->prepare('select * 
from users where login = :l');
        $stmt->bindValue(':l', $login);
        $stmt->execute();
        return $stmt->fetch();
    }

    function verify($login, $password) {
        $user = $this->getByLogin($login);
        if ($user) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return null;
    }

}
