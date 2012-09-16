<?php
// tests for our mini-app

/**
 * Test Case for the get_a_quote function
 **/
class GetQuoteCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {   
        //mock the site crawler
        $this->site_crawler = $this->getMock('ContoErotico');
    }

    /**
     * Test a successful retrieve a porn quote in json format
     */
    public function test_get_a_quote_success()
    {
        $fake_quote = array('title' => 'Ted, o ursinho saliente',
            'content' => 'Ted blá, blá, uma asinha de frango... oh no!');
        //config crawler
        $this->
            site_crawler->
            expects($this->any())->
            method('getQuote')->
            will($this->returnValue($fake_quote));
        // get json
        $quote_json = get_a_quote($this->site_crawler);
        //test the return
        $this->assertEquals(json_encode($fake_quote), $quote_json,
            "We could not retrieve the quote");
    }

    public function test_get_a_quote_failure()
    {
        //mocking site crawler config
        $this->
            site_crawler->
            expects($this->any())->
            method('getQuote')->
            will($this->throwException(new SiteCrawlerException()));
        // get json
        $quote_json = get_a_quote($this->site_crawler);
        //test the keys
        $this->assertEquals(json_encode(SiteCrawler::$bad_output), $quote_json,
            "Oops we were expecting a bad response");
    }
} // END class GetQuoteCase extends \PHPUnit_Framework_TestCase

/**
 * Test case for ContoErotico
 **/
class ContoEroticoCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Get a quote success test
     */
    public function test_get_a_quote_success()
    {
        //test site location
        ContoErotico::$site_location = RPQROOT .
            '/garbage/sample_site/index.html';
        $site_crawler = new ContoErotico();
        //get a quote
        $quote = $site_crawler->getQuote();
        //test it's keys
        $this->assertEquals(array('title', 'content'), array_keys($quote),
            "Missing array keys for getQuote");
    }

    /**
     * Not found quote test
     */
    public function test_get_a_quote_failure()
    {
        //test site location
        ContoErotico::$site_location = RPQROOT .
            '/garbage/sample_site/empty.html';
        $site_crawler = new ContoErotico();
        //that is what we want!
        $this->setExpectedException('SiteCrawlerException');
        //we should see an exception here
        $site_crawler->getQuote();
    }
} // END class ContoEroticoCase extends \PHP