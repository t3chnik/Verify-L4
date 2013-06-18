<?php
namespace T3chnik\Verifyl4Mongolid\Models;

class BaseModel extends \Zizaco\MongolidLaravel\MongoLid
{
    /**
     * Table prefix
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Create a new Mongolid model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
        
        // Set the prefix
        $this->prefix = \Config::get('verify-l4-mongolid::prefix', 'test');
        $this->collection = $this->prefix.$this->collection;
    }
    
}