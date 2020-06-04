<?

class Request_b24
{

    public function singleRequest($domain, $auth, $method, $params)
    {

        $queryUrl = 'https://' . $domain . '/rest/' . $method . '?auth=' . $auth;

        $queryData = http_build_query($params);

        $result = $this->curlRequest($queryUrl, $queryData);

        return $result;
    }

    public function curlRequest($queryUrl, $queryData)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_POST => 1,
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $queryUrl,
            CURLOPT_POSTFIELDS => $queryData,
        ));

        $results = curl_exec($curl);
        curl_close($curl);
        writeToLog($results);
        $result = json_decode($results, true);
        return $result;
    }

    public function callbatchFilter($domain, $auth, $batch)
    {
        if (count($batch) < 50) {
            return $this->callbatchRequest($domain, $auth, $batch);
        } else {
            $filter_batch = array_chunk($batch, 50);
            foreach ($filter_batch as $item) {
                return $this->callbatchRequest($domain, $auth, $item);
            }
        }
    }

    public function callbatchRequest($domain, $auth, $batch)
    {

        $queryUrl = 'https://' . $domain . '/rest/batch.json?auth=' . $auth;

        $params = array('cmd' => $batch);

        $queryData = http_build_query(
            $params
        );

        $batch_result = $this->curlRequest($queryUrl, $queryData);

        if (count($batch_result['result']['result_next']) != 0) {
            $array_key = [];
            $result_array = [];
            foreach ($batch_result['result']['result_next'] as $key => $item) {
                $array_key[$key] = [];
                $count_next = ceil($batch_result['result']['result_total'][$key] / 50);
                for ($i = 1; $i < $count_next; $i++) {
                    $batch[$key . '_' . $i] = '' . $key . '?' . http_build_query(array('select' => array('UF_*', '*'), 'more' => true, 'start' => $i * 50));
                    array_push($array_key[$key], $key . '_' . $i);
                }
            }

            $params = array('cmd' => $batch);

            $queryData = http_build_query(
                $params
            );

            $batch_result = $this->curlRequest($queryUrl, $queryData);

            foreach ($batch_result['result']['result'] as $key => $item) {
                if (count($array_key[$key]) != 0) {
                    foreach ($batch_result['result']['result'][$key] as $itemkey) {
                        $result_array[$key][] = $itemkey;
                    }
                    foreach ($array_key[$key] as $keyitem) {
                        foreach ($batch_result['result']['result'][$keyitem] as $reitemkey) {
                            $result_array[$key][] = $reitemkey;
                        }
                    }
                } else {
                    $pieces = explode("_", $key)[0];
                    if (array_key_exists($pieces, $array_key) != 1) {
                        $result_array[$key] = $batch_result['result']['result'][$key];
                    }
                }
            }
            return $result_array;

        } else {

            foreach ($batch_result['result']['result'] as $key => $item) {
                $result_array[$key] = $batch_result['result']['result'][$key];
            }

            return $result_array;
        }

    }

}

