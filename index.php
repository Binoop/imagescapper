<?php

require_once 'vendor/autoload.php';
use Goutte\Client;

$client = new \Goutte\Client();

$base_url = "https://www.ignatius.com";
// Create and use a guzzle client instance that will time out after 90 seconds
$guzzleClient = new \GuzzleHttp\Client(array(
'timeout' => 90,
'verify' => false,
));

$client->setClient($guzzleClient);

// Create ISBN array
$isbnArray = ["9781586177225",
"9781621641735",
"9781621642299",
"9781621642046",
"9781621641452",
"9781521641575",
"9781621642312",
"9781621642107",
"9781621641469",
"9781621642336",
"9780999375655",
"9781621641681",
"97810621641728",
"9781621641872",
"9781621641643",
"9781621641964",
"9781621642039",
"9781621642152",
"9781621641957",
"9781621642237"];

$successfulScrap = [];
$failureScrap = [];
foreach($isbnArray as $isbn){

try{
  // Make Isbn Search Request
  $crawler = $client->request('GET', 'https://www.ignatius.com/AdvancedSearch/DefaultWFilter.aspx?SearchTerm='.$isbn.'&ck=t,a');

  // Get Book default Page
  $value = $crawler->evaluate('//div[@class="list-item"]');
  $anchor = $value->filter('a[class="list-item-image"]');

  // Obtain Scrapper URL :
  $scrapper_url = $base_url.$anchor->attr("href");


  // Make Page Request
  $metaScrapperCrawler = $client->request('GET',$scrapper_url);

  // Filter Meta Information
  $metaScrapperCrawlerImage = $metaScrapperCrawler->filter('meta[property="og:image"]')->attr("content");


  // Set up logic to Save Image locally or In database
  $file = file_get_contents($metaScrapperCrawlerImage);
  $path = "images/".basename($metaScrapperCrawlerImage);
  $insert = file_put_contents($path, $file);

  if($insert){
    array_push($successfulScrap,$isbn);
  }
}catch(Exception $e){
  array_push($failureScrap,$isbn);
};

}
echo "Success";
print_r($successfulScrap);
echo "Failure";
print_r($failureScrap);
