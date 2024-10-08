<?php

namespace Opencart\Admin\Model\Extension\Opencart\Setting;

use Opencart\System\Engine\Model;

class Event extends Model
{
    public function addEvent($data)
    {
        // Adds a new event to the database
        $this->db->query("INSERT INTO `" . DB_PREFIX . "event` (`code`, `description`, `trigger`, `action`, `status`, `sort_order`) VALUES ('" . $this->db->escape($data['code']) . "', '" . $this->db->escape($data['description']) . "', '" . $this->db->escape($data['trigger']) . "', '" . $this->db->escape($data['action']) . "', '" . (int)$data['status'] . "', " . (int)$data['sort_order'] . ")");
    }

    public function deleteEventByCode($code)
    {
        // Deletes an event based on the code
        $this->db->query("DELETE FROM `" . DB_PREFIX . "event` WHERE `code` = '" . $this->db->escape($code) . "'");
    }

    public function getEvents()
    {
        // Retrieves all events from the database
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "event`");
        return $query->rows;
    }

    public function getEvent($event_id)
    {
        // Retrieves a specific event by its ID
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "event` WHERE `event_id` = '" . (int)$event_id . "'");
        return $query->row;
    }
}
