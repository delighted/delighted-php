<?php

namespace Delighted;

class Resource implements JSONSerializable
{

    protected $__data = [];

    public static function identifierString($identifierAssoc = array()) {
        if (count($identifierAssoc) != 1) {
            throw new \InvalidArgumentException("You must pass exactly one identifier");
        }

        $identifierName = reset(array_keys($identifierAssoc));
        $identifierValue = $identifierAssoc[$identifierName];

        if (strval($identifierName) == "id") {
            return strval($identifierValue);
        } elseif (isset($identifierName) && isset($identifierValue)) {
            return $identifierName . ":" . $identifierValue;
        } else {
            return;
        }
    }

    public function __construct($data = [])
    {
        if (isset($this->expandable)) {
            foreach ($this->expandable as $key => $class) {
                if (isset($data[$key]) && is_array($data[$key])) {
                    $data[$key] = new $class($data[$key]);
                }
            }
        }
        $this->__data = $data;
    }

    public function __get($k)
    {
        if (array_key_exists($k, $this->__data)) {
            return $this->__data[$k];
        } else {
            return null;
        }
    }

    public function __set($k, $v)
    {
        if (array_key_exists($k, $this->__data)) {
            $this->__data[$k] = $v;
        }
    }

    public function __isset($k)
    {
        return array_key_exists($k, $this->__data);
    }

    public function __unset($k)
    {
        if (array_key_exists($k, $this->__data)) {
            unset($this->__data[$k]);
        }
    }

    public function jsonSerialize()
    {
        $data = $this->__data;
        foreach ($data as $k => $v) {
            if (is_subclass_of($v, '\Delighted\JSONSerializable')) {
                $data[$k] = $v->jsonSerialize();
            }
        }

        return $data;
    }

    protected function doJsonSerialize($values)
    {
        foreach ($values as $k => $v) {
            if (is_subclass_of($v, '\Delighted\JSONSerializable')) {
                $values[$k] = $v->jsonSerialize();
            }
        }

        return $values;
    }

}
