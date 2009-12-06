<?php

//Reference Documentation: http://support.chargify.com/faqs/api/api-products

class ChargifyProduct extends ChargifyConnector
{
  private $price_in_cents;
  private $name;
  private $handle;
  private $product_family = array('name' => NULL, 'handle' => NULL, 'accounting_code' => NULL);
  private $accounting_code;
  private $interval_unit;
  private $interval;

  public function __construct(SimpleXMLElement $product_xml_node)
  {  
    //Load object dynamically and convert SimpleXMLElements into strings
    foreach($product_xml_node as $key => $element) { $this->$key = (string)$element; }
  }
  
  
  /* Getters */
  
  public function getPriceInCents() { return $this->price_in_cents; }
  
  public function getPriceInDollars() { return number_format($this->price_in_cents / 100, 0); }
  
  public function getName() { return $this->name; }
  
  public function getHandle() { return $this->handle; }
  
  public function getProductFamily() { return $this->product_family; }
  
  public function getAccountCode() { return $this->accounting_code; }
  
  public function getIntervalUnit() { return $this->interval_unit; }
  
  public function getInterval() { return $this->interval; }

}