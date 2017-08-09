<?php namespace JULIET\API\Common\Model;


class JulietModel implements \ArrayAccess, \JsonSerializable  {
    
    /**
    * Contains our model as an array
    */
    private $_model = [];
    
    /**
    * Array containing the type information for the members of the model
    * will be used to cast property member to the right type
    *
    * example :
    * [
    *  id => 'integer',
    *  name => 'string',
    *  group_id => 'integer',
    *  has_rights => 'boolean'
    * ]
    */
    protected $_type;
    
    /**
    * Create the model with the given array/object
    *
    * @param array|object $modelToBind
    */
    public function __construct($modelToBind) {
        foreach($modelToBind as $pp => $val)
        $this->__set($pp, $val);
        
    }
    
    public function __set($name, $value) {
        // Get the type if needed
        if($this->_type && isset($this->_type[$name])) settype($value, $this->_type[$name]);
        // Set the model
        $this->_model[$name] = $value;
    }
    
    public function __get($name) {
        return $this->_model[$name];
    }
    
    public function offsetExists ( $offset ) {
        return !empty($this->__get($offset));
    }
    
    public function offsetGet ( $offset ) {
        return $this->__get($offset);
    }
    
    public function offsetSet ( $offset , $value ) {
        $this->__set($offset, $value);
    }
    
    public function offsetUnset ( $offset ) {
        return !empty($this->__get($offset));
    }
    
    public function jsonSerialize() {
        $reflection = new \ReflectionObject($this);
        $properties = $reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        $ret = [];
        
        foreach($properties as $pp) {
            $pd = $pp->getName();
            $ret[$pd] = $this[$pd];
        }
        
        return $ret;
    }
    
    
    
}