<?php

namespace Opencart\Admin\Model\Extension\Opencart\Setting;

use Opencart\System\Engine\Model;

class Setting extends Model
{
    public function getSetting($code)
    {
        // Retrieves all settings matching the provided code
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `code` = '" . $this->db->escape($code) . "'");
        return $query->rows;
    }

    public function editSetting($code, $data)
    {
        // Edits or saves settings for a specific code
        foreach ($data as $key => $value) {
            // Check if the setting already exists
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `key` = '" . $this->db->escape($key) . "'");
            if ($query->num_rows) {
                // Update existing setting
                $this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `value` = '" . $this->db->escape($value) . "' WHERE `key` = '" . $this->db->escape($key) . "'");
            } else {
                // Insert new setting
                $this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`key`, `value`, `code`) VALUES ('" . $this->db->escape($key) . "', '" . $this->db->escape($value) . "', '" . $this->db->escape($code) . "')");
            }
        }
    }

    public function deleteSetting($key)
    {
        // Deletes a setting based on the key
        $this->db->query("DELETE FROM `" . DB_PREFIX . "setting` WHERE `key` = '" . $this->db->escape($key) . "'");
    }
}
