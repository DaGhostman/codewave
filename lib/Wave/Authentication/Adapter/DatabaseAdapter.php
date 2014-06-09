<?php

namespace Wave\Authentication\Adapter;

class DatabaseAdapter extends AbstractAdapter
{
    protected $table = null;
    protected $credentialFiled = null;
    protected $secretField = null;
    
    protected $database = null;
    
    
    /**
     * Defines the table and fields which are to be used for authentication
     * 
     * @param string $table The table to be used for authentication
     * @param array $fileds numeric array, 0 => credential filed, 1 => secret field
     */
    public function __construct($table, $fileds)
    {
        list($this->credentialFiled, $this->secretField)= $fileds;
        
        $this->table = $table;
    }
    
    /**
     * Injects the database handler in to the authentication adapter
     * 
     * @param \Wave\Database\Engine $db The database handler
     * @return object Current instances
     * @throws \InvalidArgumentException
     */
    public function setDatabase($db)
    {
        if (!$db instanceof \Wave\Database\Engine) {
            throw new \InvalidArgumentException(
	           "Expected instance of Wave\Database\Engine"
            );
        }
        
        $this->database = $db;
        
        return $this;
    }
    
    /**
     * (non-PHPdoc)
     * @see \Wave\Authentication\Adapter\AbstractAdapter::get()
     */
    public function get($auth_string)
    {
        list($credential, $secret)=explode('[:]', $auth_string);
        
        $condition = "%s = :credential AND %s = :secret";
        
        return $this->database->select($this->table, array('*'))
            ->where(sprintf($condition, $this->credentialFiled, $this->secretField))
            ->bind(':credential', $credential, null)
            ->bind(':secret', $secret, null)
            ->execute()
            ->fetch();
    }
}