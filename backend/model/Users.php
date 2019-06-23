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

    function inRoom($roomId) {
        $stmt = $this->db->prepare('SELECT * FROM users JOIN in_room ON (in_room.id_users=users.id_users) 
WHERE in_room.id_rooms = :roomId');
        $stmt->bindValue(':roomId', $roomId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function allUsers() {
        $stmt = $this->db->prepare('select * 
from users');
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function kickUser($userId, $roomId) {
        $stmt = $this->db->prepare(
            'INSERT INTO room_kick (id_users, id_rooms, created) VALUES (:userId, :roomId, NOW())');
        $stmt->bindValue(':roomId', $roomId);
        $stmt->bindValue(':userId', $userId);
        return $stmt->execute();
    }

    function removeKick($userId, $roomId) {
        $stmt = $this->db->prepare(
            'DELETE FROM room_kick WHERE id_users = :userId AND id_rooms = :roomId');
        $stmt->bindValue(':roomId', $roomId);
        $stmt->bindValue(':userId', $userId);
        return $stmt->execute();
    }

    function updateUser($userLogin, $name, $surname, $email, $gender) {
        $stmt = $this->db->prepare(
            'UPDATE users SET name = :name, surname = :surname, email = :email, gender = :gender WHERE login = :userLogin');
        $stmt->bindValue(':userLogin', $userLogin);
        $stmt->bindValue(':name', $name);
        $stmt->bindValue(':surname', $surname);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':gender', $gender);
        $stmt->execute();
    }

}
