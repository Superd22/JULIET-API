<?php namespace JULIET\Calendar\helper;
  class aArrayAccess implements \ArrayAccess {
    public function offsetSet($offset, $value) {
      if (is_null($this->{$offset}))
        $this->{$offset} = $value;
      else
        $this->{$offset} = $value;
    }

    public function offsetExists($offset) {
      return isset($this->{$offset});
    }

    public function offsetUnset($offset) {
      unset($this->{$offset});
    }

    public function offsetGet($offset) {
      return isset($this->{$offset}) ? $this->{$offset} : null;
    }
  }
