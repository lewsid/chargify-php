<?php

/******************************************************************************************

  Christopher Lewis, 2010

  Reference Documentation: http://support.chargify.com/faqs/api/api-authentication

******************************************************************************************/

class ChargifyConnector
{
  protected $api_key        = 'ENTER API KEY HERE';
  protected $test_api_key   = 'ENTER TEST API KEY HERE';
  protected $domain         = 'ENTER THE DOMAIN HERE';
  protected $test_domain    = 'ENTER THE TEST DOMAIN HERE';
  
  protected $active_api_key;
  protected $active_domain;
  protected $test_mode;
  
  public function __construct($test_mode = false)
  {
    $this->test_mode = $test_mode;
    
    if($test_mode)
    {
      $this->active_api_key = $this->test_api_key;
      $this->active_domain  = $this->test_domain;
    }
    else
    {
      $this->active_api_key = $this->api_key;
      $this->active_domain  = $this->domain;
    }
  }
  
  public function retrieveAllCustomersXML($page_num = 1)
  {
    return $this->sendRequest('/customers.xml?page=' . $page_num);
  }
  
  public function retrieveCustomerXMLByID($id)
  {
    return $this->sendRequest('/customers/' . $id . '.xml');
  }
  
  public function retrieveCustomerXMLByReference($ref)
  {
    return $this->sendRequest('/customers/lookup.xml?reference=' . $ref);
  }
  
  public function retrieveSubscriptionsXMLByCustomerID($id)
  {
    return $this->sendRequest('/customers/' . $id . '/subscriptions.xml');
  }
  
  public function retrieveAllProductsXML()
  {
    return $this->sendRequest('/products.xml');
  }
  
  /*
     Example post xml:     
 
     <?xml version="1.0" encoding="UTF-8"?>
      <subscription>
        <product_handle>' . $product_id . '</product_handle>
        <customer_attributes>
          <first_name>first</first_name>
          <last_name>last</last_name>
          <email>email@email.com</email>
        </customer_attributes>
        <credit_card_attributes>
          <first_name>first</first_name>
          <last_name>last</last_name>
          <billing_address>1 Infinite Loop</billing_address>
          <billing_city>Somewhere</billing_city>
          <billing_state>CA</billing_state>
          <billing_zip>12345</billing_zip>
          <billing_country>USA</billing_country>
          <full_number>41111111111111111</full_number>
          <expiration_month>11</expiration_month>
          <expiration_year>2012</expiration_year>
        </credit_card_attributes>
      </subscription>
  */
  /**
   * @return SimpleXMLElement|ChargifySubscription
   */
  public function createCustomerAndSubscription($post_xml)
  {
    $xml = $this->sendRequest('/subscriptions.xml', $post_xml);

    $tree = new SimpleXMLElement($xml);

    if(isset($tree->error)) { return $tree; }
    else { $subscription = new ChargifySubscription($tree); }
    
    return $subscription;
  }
  
  public function getAllCustomers()
  {
    $xml = $this->retrieveAllCustomersXML();
    
    $all_customers = new SimpleXMLElement($xml);
   
    $customer_objects = array();
    
    foreach($all_customers as $customer)
    {
      $temp_customer = new ChargifyCustomer($customer);
      array_push($customer_objects, $temp_customer);
    }
    
    return $customer_objects;
  }
  
  /**
   * @return ChargifyCustomer
   */
  public function getCustomerByID($id)
  {
    $xml = $this->retrieveCustomerXMLByID($id);
    
    $customer_xml_node = new SimpleXMLElement($xml);
    
    $customer = new ChargifyCustomer($customer_xml_node);
    
    return $customer;
  }
  
  /**
   * @return ChargifyCustomer
   */
  public function getCustomerByReference($ref)
  {
    $xml = $this->retrieveCustomerXMLByReference($ref);

    $customer_xml_node = new SimpleXMLElement($xml);
    
    $customer = new ChargifyCustomer($customer_xml_node);
        
    return $customer;
  }
  
  public function getSubscriptionsByCustomerID($id)
  {
    $xml = $this->retrieveSubscriptionsXMLByCustomerID($id);
    
    $subscriptions = new SimpleXMLElement($xml);
    
    $subscription_objects = array();
    
    foreach($subscriptions as $subscription)
    {
      $temp_sub = new ChargifySubscription($subscription);
      
      array_push($subscription_objects, $temp_sub);
    }
    
    return $subscription_objects;
  }
  
  public function getAllProducts()
  {
    $xml = $this->retrieveAllProductsXML();
    
    $all_products = new SimpleXMLElement($xml);
   
    $product_objects = array();
    
    foreach($all_products as $product)
    {
      $temp_product = new ChargifyProduct($product);
      array_push($product_objects, $temp_product);
    }
    
    return $product_objects;
  }
  
  /**
   * @return ChargifyCustomer
   */
  public function createCustomer($post_xml) {
    $xml = $this->sendRequest('/customers.xml', $post_xml);
    
    $customer_xml_node = new SimpleXMLElement($xml);
    
    $customer = new ChargifyCustomer($customer_xml_node);
    
    return $customer;
  }

  protected function curlArguments($uri, $post_xml = null) {
    $args[] = "-u {$this->active_api_key}:x";
    $args[] = "-H Content-Type:application/xml";
    $args[] = "https://{$this->active_domain}.chargify.com{$uri}";
    
    if ($post_xml) {
      $args[] = "--data-binary \"$post_xml\"";
    }
    
    return $args;
  }
  
  protected function sendRequest($uri, $post_xml = null) {    
    exec('curl ' . join(' ', $args), $output);
    $xml = implode("\n", $output);
    
    return $xml;
  }
}