<?php

/******************************************************************************************

  Christopher Lewis, 2009

  Reference Documentation: http://support.chargify.com/faqs/api/api-authentication

******************************************************************************************/

class ChargifyConnector
{
  private $api_key        = 'ENTER API KEY HERE';
  private $test_api_key   = 'ENTER TEST API KEY HERE';
  private $domain         = 'ENTER THE DOMAIN HERE';
  private $test_domain    = 'ENTER THE TEST DOMAIN HERE';
  
  private $active_api_key;
  private $active_domain;
  private $test_mode;
  
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
    exec('curl -u ' . $this->active_api_key . ':x https://' . $this->active_domain 
      . '.chargify.com/customers.xml?page=' . $page_num, $output);
    $xml = implode("\n", $output);
    
    return $xml;
  }
  
  public function retrieveCustomerXMLByID($id)
  {
    exec('curl -u ' . $this->active_api_key . ':x https://' . $this->active_domain . '.chargify.com/customers/' 
      . $id . '.xml', $output);
    $xml = implode("\n", $output);
    
    return $xml;
  }
  
  public function retrieveSubscriptionsXMLByCustomerID($id)
  {
    exec('curl -u ' . $this->active_api_key . ':x https://' . $this->active_domain . '.chargify.com/customers/' 
      . $id . '/subscriptions.xml', $output);
    $xml = implode("\n", $output);
    
    return $xml;
  }
  
  public function retrieveAllProductsXML()
  {
    exec('curl -u ' . $this->active_api_key . ':x https://' . $this->active_domain . '.chargify.com/products.xml', $output);
    $xml = implode("\n", $output);
    
    return $xml;
  }
  
  public function createCustomerAndSubscription($post_xml)
  {
    exec('curl -u ' . $this->active_api_key . ':x -H Content-Type:application/xml https://' . $this->active_domain 
      . '.chargify.com/subscriptions.xml --data-binary "' . $post_xml . '"', $output);
    
    $xml = implode("\n", $output);
    
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
  
  public function getCustomerByID($id)
  {
    $xml = $this->retrieveCustomerXMLByID($id);
    
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
}

?>