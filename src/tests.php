<?php
// tests for our mini-app

class PlayCrawler extends SiteCrawler {
    public function getQuote()
    {
        //nothing
    }
}

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

    /**
     * test the returning value for a not found quote error
     */
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
* Site crawler functions
*/
class SiteCrawlerCase extends \PHPUnit_Framework_TestCase
{
    /**
     * read with full url
     */
    public function test_read_fullurl()
    {
        // crawler config
        PlayCrawler::$site_location = 'http://fake.site.com/fuzziness/bla';
        // the site crawler
        $crawler = new PlayCrawler();
        // full url
        $url = 'http://fake.site.com/another';
        // should return the original url
        $this->assertEquals($url, $crawler->normatize($url),
            'read with full url error');
    }

    /**
     * read with relative url
     */
    public function test_read_relative()
    {
        // crawler config
        PlayCrawler::$site_location = 'http://fake.site.com/fuzziness/bla';
        // the site crawler
        $crawler = new PlayCrawler();
        // full url
        $relative = 'just/for/fun';
        // should return the original url
        $this->assertEquals('http://fake.site.com/' . $relative ,
            $crawler->normatize($relative), 'read with relative url error');
    }
}

/**
 * Test case for ContoErotico
 **/
class ContoEroticoCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Mock object setup
     */
    public function setUp()
    {
        // replace the normatize method of ContoErotico  
        $site_crawler = $this->getMock('ContoErotico', array('normatize'));
        $site_crawler->expects($this->any())
             ->method('normatize')
             ->will($this->returnCallback(function ($argument) {
                return RPQROOT . '/garbage/sample_site/' . $argument;
            }));

        $this->crawler = $site_crawler;
    }

    /**
     * Test the returning keys for get a quote
     */
    public function test_get_a_quote_returning_keys()
    {
        //test site location
        ContoErotico::$site_location = 'index.html';
        //get a quote
        $quote = $this->crawler->getQuote();
        //test it's keys
        $this->assertEquals(array('title', 'content'), array_keys($quote),
            "Missing array keys for getQuote");
    }

    /**
     * No quote text found in the quote page test
     *
     * @expectedException SiteCrawlerException
     */
    public function test_get_a_quote_leadtonowhere()
    {
        //test site location
        ContoErotico::$site_location = 'leadtonowhere.html';
        // too bad, no text found in the page of the quote
        $this->crawler->getQuote();
    }

    /**
     * No links to tales should throw a SiteCrawlerException
     *
     * @expectedException SiteCrawlerException
     */
    public function test_get_quote_linksnotfound()
    {
        //test site with no html
        ContoErotico::$site_location = 'empty.html';
        //It should throw an exception cuz we can't found the tales links
        $this->crawler->getQuote();
    }
} // END class ContoEroticoCase extends \PHP