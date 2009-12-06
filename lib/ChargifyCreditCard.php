<?php

//Reference Documentation: http://support.chargify.com/faqs/api/api-subscriptions

class ChargifyCreditCard extends ChargifyConnector
{
  private $card_type;
  private $expiration_month;
  private $expiration_year;
  private $first_name;
  private $last_name;
  private $masked_card_number;

  public function __construct(SimpleXMLElement $cc_xml_node)
  {  
    //Load object dynamically and convert SimpleXMLElements into strings
    foreach($cc_xml_node as $key => $element) { $this->$key = (string)$element; }
  }
  
  
  /* Getters */
  
  public function getCardType() { return $this->card_type; }
  
  public function getExpirationMonth() { return $this->expiration_month; }
  
  public function getExpirationYear() { return $this->expiration_year; }
  
  public function getFirstName() { return $this->first_name; }
  
  public function getLastName() { return $this->last_name; }
  
  public function getMaskedCardNumber() { return $this->masked_card_number; }
}