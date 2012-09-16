<?php
// libs, bussiness logic, etc...

use Symfony\Component\DomCrawler\Crawler;

class SiteCrawlerException extends \Exception {}

/**
 * Crawls a site looking a for a quote paragraph
 */
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
     * @todo categorize and return a quote by a "porn metter"
     * @param array eg: array('title' => '...', 'content' => '...')
     **/
    abstract public function getQuote();
}

/**
 * http://www.contoerotico.com.br/categorias/heterosexual/ crawler
 */
class ContoErotico extends SiteCrawler 
{
    public static $site_location = 
        'http://www.contoerotico.com.br/categorias/heterosexual/';

    public function getQuote()
    {   
        //dom crawler
        $crawler = new Crawler(file_get_contents(self::$site_location));
        //search tale links
        $tale_links = $crawler->filter('.content_left h3 a');
        // get a tale
        $random_tale = $tale_links->eq(rand(0, count($tale_links) - 1));
        // go to the tale page
        $crawler = new Crawler(file_get_contents($random_tale->attr('href')));
        // get the tale text
        $tale_text = $crawler->filter('.single-main p')->text();

        //limit the caracter number of the text
        $text_limit = 500;
        $text_len = strlen($tale_text);
        if ($text_len > $text_limit) {
            $sort_limit = $text_len - $text_limit;
            $min = rand(0, $sort_limit - 1);
            $tale_text = substr($tale_text, $min, $text_limit);
        }

        //return a random paragraph
        return array('title' => $random_tale->text(),
            'content' => $tale_text);
    }
}

/**
 * Return a random quote in json format
 *
 * @param SiteCrawler  The one that will do tha find magic
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