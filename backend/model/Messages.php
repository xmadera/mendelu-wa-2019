<?php

class Messages extends Model {

    function getAllByRoomId($roomId) {
        $stmt = $this->db->prepare('SELECT * FROM messages JOIN users ON (users.id_users = messages.id_users_from)
WHERE id_rooms = :id ORDER BY created');
        $stmt->bindValue(':id', $roomId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    function saveMessage($idRoom, $idOwner, $message) {
        $stmt = $this->db->prepare('INSERT INTO messages '
            . '(id_rooms, id_users_from, id_users_to, created, message)'
            . ' VALUES '
            . '(:idRoom, :idOwner, NULL, NOW(), :message)');
        $stmt->bindValue(':idRoom', $idRoom);
        $stmt->bindValue(':idOwner', $idOwner);
        $stmt->bindValue(':message', $message);
        return $stmt->execute();
    }

}
