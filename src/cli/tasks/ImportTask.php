<?php

use Phalcon\Cli\Task;

/**
 * Usage: ENV=dev php cli/cli.php import
 * Class ImportTask
 */
class ImportTask extends Task
{
  public function mainAction()
  {
    message("Import process started");
    $filePath = sprintf("%s/data.json", dirname(__DIR__));
    message(sprintf("Reading file in \"%s\"", $filePath));
    $properties = json_decode(file_get_contents($filePath), true);
    foreach ($properties as $property)
    {
      $response = doCurlCall($property);
      if ($response)
      {
        message(sprintf("property \"%s\" added/updated", $property["name"]));
      }
      else
      {
        message(sprintf("property \"%s\" not added/updated", $property["name"]));
      }
    }

    message(sprintf("Process finished, processed %s properties", count($properties)));
  }
}

function message($message)
{
  echo $message . PHP_EOL;
}

function doCurlCall($property)
{
  $name = $property["name"];
  $price = (int) $property["price"];
  $type = $property["type"];

  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL,"http://172.28.1.3:83/property");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS,
    "name=$name&price=$price&type=$type");

  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $response = curl_exec($ch);
  curl_close ($ch);

  return $response;
}
