<?php

// Fetch the HTML content from the other server
$html = file_get_contents('http://localhost:4000/date.php');

// Use DOMDocument to parse the HTML
$dom = new DOMDocument;
libxml_use_internal_errors(true); // Suppress warnings
$dom->loadHTML($html);
libxml_clear_errors();

// Use XPath to find the <script> tag with the JSON data
$xpath = new DOMXPath($dom);
$script_tags = $xpath->query('//script[@type="application/ld+json"]');

// Extract the JSON data from the first <script> tag
$json_data = $script_tags->item(0)->nodeValue;

// Decode the JSON data into a PHP array
$data = json_decode($json_data, true);

// Now you can use the data...
print_r($data);


function sendToRDF4J($data) {
    $url = 'http://localhost:8080/rdf4j-server/repositories/web-semantic';
    $jsonData = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/sparql-update'
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    $result = curl_exec($ch);
    if (curl_errno($ch)) {
        echo "Curl error: " . curl_error($ch);
    }
    curl_close($ch);

    return $result;
}

?>
