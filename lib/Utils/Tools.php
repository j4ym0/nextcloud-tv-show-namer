<?php
namespace OCA\TVShowNamer\Utils;


class Tools {
    /**
    * fetch the data from api
    * @param path $url of the api to query
    * @param parameters $params in a key => value format, will be phrased in func
    * @param post_data $data to post to the api, if null will be a get request
    * @since 1.0.0
    * @return results as json
    */
    static function api_call($path, $api_key = null, $bearer_token = null, $params = null, $data = null, $data_type = null){
        # remove first / if there
        if (substr($path, 0, 1) === '/'){
            $path = substr($path, 1);
        }

        $request = ['http' => 
            [
                'method' => 'GET',
                'ignore_errors' => true,
            ],
        ];
        $query_string = '';
        $headers = [];
  
        // check for reed / RW token
        if ($bearer_token != null){
            # add the bearer token to the header
            $headers = array_merge(['Authorization: Bearer ' . $bearer_token], $headers);
        }elseif ($api_key != null){
            $params['api_key'] = $api_key;
        }
  
        # process get params
        if ($params != null){
            foreach ($params as $key => $value){
                // check for previous keys added to query string
                if (strlen($query_string) > 0){
                    $query_string .= '&';
                }else{
                    $query_string .= '?';
                }
                $query_string .= $key . '=' . urlencode($value);
            }
        }
  
        # build post data if available
        if ($data != null){
            $request['http']['method'] = 'POST';

            # check if the data is json
            if ($data_type == 'json'){
                $headers = array_merge(['Content-type: application/json',], $headers);
                $request['http']['content'] = json_encode($data);
            }else{
                $request['http']['content'] = http_build_query($data);
            }
        }
  
        $request['http']['header'] = implode("\r\n", $headers);
        # build http request
        $context = stream_context_create($request);
        $json = file_get_contents($path . $query_string, false, $context);
        return json_decode($json, true);
    }
}