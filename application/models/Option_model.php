<?php
/**
 * Created by PhpStorm.
 * User: Mike
 * Date: 29/01/2018
 * Time: 14:16
 */

class Option_model extends CI_Model{
    public function __construct()
    {
        parent::__construct();
    }

    private function get_option_groups()
    {
        return array(
            'siteLogo',
            'siteAvatar',
            'siteName',
            'siteDescription',
            'siteBackgroundImage',
            //'siteDefaultComplainCover'
            //'siteBackgroundImage',
        );
    }

    public function update_all_options($data)
    {
        if (!empty($data)) {
            foreach ($data as $key => $value) {
                $this->update_option($key, $value);
            }
        }
    }

    public function update_option($key, $value)
    {
        if (!empty($key) && !empty($value)) {
            $query = $this->db->get_where('options', array('key' => $key));
            if (empty($query->result_array())) {
                $this->db->insert('options', array(
                    'key' => $key,
                    'value' => maybe_serialize($value)
                ));
            } else {
                $this->db->where(array('key' => $key));
                $this->db->update('options', array('value' => maybe_serialize($value)));
            }
        }
    }

    public function get_option($key)
    {
        if (!empty($key)) {
            $query = $this->db->get_where('options', array('key' => $key))->row();
            if ($value=maybe_null_or_empty($query, 'value'))
                return maybe_unserialize($value);
            return false;
        }
    }

    public function get_options()
    {
        $groups = $this->get_option_groups();
        $temp = array();
        if (!empty($groups)) {
            foreach ($groups as $option_key) {
                $temp[$option_key] = $this->get_option($option_key);
            }
        }
        return $temp;
    }
}