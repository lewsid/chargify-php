<?php

//Reference Documentation: http://support.chargify.com/faqs/api/api-customers

class ChargifyCustomer extends ChargifyConnector
{
  private $id;
  private $created_at;
  private $email;
  private $first_name;
  private $last_name;
  private $organization;
  private $reference;
  private $updated_at;
  
  public function __construct(SimpleXMLElement $customer_xml_node)
  {  
    //Load object dynamically and convert SimpleXMLElements into strings
    foreach($customer_xml_node as $key => $element) { $this->$key = (string)$element; }
  }
  
  
  /* Getters - I like to do this the old-fashioned way */
  
  public function getID() { return $this->id; }
  
  public function getCreatedAt() { return $this->created_at; }
  
  public function getFirstName() { return $this->first_name; }
  
  public function getLastName() { return $this->last_name; }
  
  public function getOrganization() { return $this->organization; }
  
  public function getReference() { return $this->reference; }
  
  public function getUpdatedAt() { return $this->updated_at; }
  
  public function getFullName() { return $this->first_name . ' ' . $this->last_name; }
}

?>