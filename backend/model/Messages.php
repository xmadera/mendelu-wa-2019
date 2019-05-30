<?php

class Messages extends Model {

    function getAllByRoomId($roomId) {
        $stmt = $this->db->prepare('SELECT * FROM messages WHERE id_rooms = :id ORDER BY created');
        $stmt->bindValue(':id', $roomId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

}
