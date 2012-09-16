<?php
// libs, bussiness logic, etc...

class SiteCrawlerException extends \Exception {}

abstract class SiteCrawler
{   
    /**
     * A sample bad output to be converted to a json
     */
    public static $bad_output = array('oops' => 'not our lucky day, huh?');
    /**
     * The site location
     */
    public static $site_location;

    /**
     * Return a random quote
     *
     * @param array eg: array('title' => '...', 'content' => '...')
     **/
    abstract public function getQuote();
}

class ContoErotico extends SiteCrawler 
{
    public function getQuote()
    {
        //do something, but later...
    }
}

/**
 * Return a random quote in json format
 *
 * @param \RPQ\SiteCrawler  The one that will do tha find magic
 * @return string  Json formated array eg: array('title' => '...', 'content' => '...')
 */
function get_a_quote(SiteCrawler $site_crawler)
{
    try {
        $quote = $site_crawler->getQuote();
    } catch (SiteCrawlerException $e) {
        // something went wrong, get the bad output
        $quote = SiteCrawler::$bad_output;
    }

    //return a json
    return json_encode($quote);
}