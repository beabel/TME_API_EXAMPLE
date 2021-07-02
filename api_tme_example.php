<?php
//---------------------------------------------------------
const TOKEN = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
const APP_SECRET = 'yyyyyyyyyyyyyyyyyyyyyyyyy'; 

//######################################################
$searchstring = 'BTA06T-600CWRG';//this can be created dynamic
//######################################################//######################################################
  // hole Lager und Preise
  $params_tme_GetPricesAndStocks = array(
      'SymbolList' => array($searchstring),
      'Currency'   => 'EUR',
      'Language'   => 'DE',
  ); 
  $response_tme_GetPricesAndStocks = api_call('Products/GetPricesAndStocks', $params_tme_GetPricesAndStocks);
  $result_tme_GetPricesAndStocks = json_decode($response_tme_GetPricesAndStocks, true);      
  
  echo '<pre>';
  print_r($result_tme_GetPricesAndStocks);
  echo '</pre>';
  
  
  
  
//######################################################//######################################################  
// TME Grundfunktionen
function api_call($action, array $params)
{
    $params['Token'] = TOKEN;
    $params['ApiSignature'] = getSignature($action, $params, APP_SECRET);

    $opts = array(
        'http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => http_build_query($params),
            ),
    );

    return file_get_contents(getUrl($action), false, stream_context_create($opts));
}

function getSignature($action, array $parameters, $appSecret)
{
    $parameters = sortSignatureParams($parameters);

    $queryString = http_build_query($parameters, null, '&', PHP_QUERY_RFC3986);
    $signatureBase = strtoupper('POST') .
        '&' . rawurlencode(getUrl($action)) . '&' . rawurlencode($queryString);

    return base64_encode(hash_hmac('sha1', $signatureBase, $appSecret, true));
}

function getUrl($action)
{
    return 'https://api.tme.eu/' . $action . '.json';
}

function sortSignatureParams(array $params)
{
    ksort($params);

    foreach ($params as &$value) {
        if (is_array($value)) {
            $value = sortSignatureParams($value);
        }
    }

    return $params;
}  
?>
