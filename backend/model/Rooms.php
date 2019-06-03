<?php

class Rooms extends Model {

    function add($title, $idOwner, $lock = 0) {
        $stmt = $this->db->prepare('INSERT INTO rooms '
                . '(created, title, id_users_owner, lock)'
                . ' VALUES '
                . '(NOW(), :t, :id, :l)');
        $stmt->bindValue(':t', $title);
        $stmt->bindValue(':id', $idOwner);
        $stmt->bindValue(':l', $lock);
        return $stmt->execute();
    }

    function delete($id) {
    $stmt = $this->db->prepare('DELETE FROM rooms WHERE id_rooms = :id');
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    }

    function all() {
        $stmt = $this->db->query('SELECT * FROM rooms ORDER BY created');
        return $stmt->fetchAll();
    }

    function find($id) {
        $stmt = $this->db->prepare('SELECT * FROM rooms WHERE id_rooms = :id');
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    function saveUserInRoom($roomId, $userId) {
        $stmt = $this->db->prepare('INSERT INTO in_room '
            . '(id_users, id_rooms, last_message, entered)'
            . ' VALUES '
            . '(:userId, :roomId, NOW(), NOW())');
        $stmt->bindValue(':roomId', $roomId);
        $stmt->bindValue(':userId', $userId);
        return $stmt->execute();
    }

    function deleteUserFromRoom($roomId, $userId) {
        $stmt = $this->db->prepare('DELETE FROM in_room WHERE id_rooms = :roomId AND id_users = :userId');
        $stmt->bindValue(':roomId', $roomId);
        $stmt->bindValue(':userId', $userId);
        $stmt->execute();
    }

}
