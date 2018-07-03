<?php

namespace App;
class AcfFields
{
    private $fields = [];
    private $curRepeater = NULL;

    public function __construct($key, $title, $location, $priority = NULL)
    {
        $this->key = $key;
        $this->title = $title;
        $this->location = $location;
        $this->priority = $priority;

    }

    public function addField($key, $data, $options = [])
    {
        $id = $key;
        if ($data[0] == 'post') {
            $data[0] = 'post_object';
        }
        $ret = [
            'key' => 'field_' . $this->key . $key . $id . ($this->getLastRepeater() ? '_' . $this->getLastRepeater()->id : ''),
            'label' => $data[1],
            'name' => $key,
            'type' => $data[0],
            'required' => 0,
        ];
        $ret = array_merge($ret, $options);
        if ($data[0] == 'select') {
            $choices = [];
            foreach ($data[2] as $key => $val) {
                if (is_integer($key)) {
                    $choices[$val] = $val;
                } else {
                    $choices[$key] = $val;

                }
            }
            $ret['ui'] = 1;
            $ret['choices'] = $choices;
        }
        $this->_addField($ret);
    }

    function addTab($title)
    {
        $id = sanitize_title($title);
        $this->_addField([
            'key' => 'field_tab_' . $id,
            'label' => $title,
            'name' => 'tab_' . $id,
            'type' => 'tab',
            'required' => 0,
        ]);
    }

    function startRepeater($id, $title)
    {
        $this->curRepeater[] = (object)[
            'key' => $this->key . $id,
            'id' => $id,
            'title' => $title,
            'fields' => []
        ];
    }

    function endRepeater($layout = 'table')
    {
        $repeater = array_pop($this->curRepeater);
        $this->_addField([
            'key' => $repeater->key,
            'label' => $repeater->title,
            'name' => $repeater->id,
            'type' => 'repeater',
            'layout' => $layout,
            'sub_fields' => $repeater->fields
        ]);
    }

    protected function getLastRepeater()
    {
        return $this->curRepeater ? $this->curRepeater[count($this->curRepeater) - 1] : null;
    }

    private function _addFields($fields)
    {
        if ($this->curRepeater) {
            $repeater = $this->getLastRepeater();
            $repeater->fields = array_merge($repeater->fields, $fields);
        } else {
            $this->fields = array_merge($this->fields, $fields);
        }
    }

    private function _addField($field)
    {
        $this->_addFields([$field]);
    }

    function register()
    {
        $args = [
            'key' => $this->key,
            'title' => $this->title,
            'fields' => $this->fields,
            'menu_order' => 0,
            'location' => [
                [
                    $this->location
                ]
            ]
        ];
        if ($this->priority) {
            $args['position'] = $this->priority;
        }
        acf_add_local_field_group($args);
    }
}
