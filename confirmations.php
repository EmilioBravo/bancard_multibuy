<?php
namespace Bancard;

class GetConfirmations
{
    protected $public;
    protected $private;
    protected $url = 'https://vpos.infonet.com.py';

    public function __construct($public, $private)
    {
        $this->public  = $public;
        $this->private = $private;
    }

    public function staging()
    {
        $this->url = 'https://vpos.infonet.com.py:8888';
        return $this;
    }
    
    public function buy()
    {
        $shop_process_id = 1497470267;
        //$shop_process_id = time();
        $token = $this->token($shop_process_id, 'get_confirmation');
        $req   = array(
            'public_key' => $this->public,
            'operation' => array(
                'token' => $token,
                'shop_process_id' => $shop_process_id,
            )
        );
       /* $_SESSION['process_id'] = $shop_process_id;
        $_SESSION['token'] = $token;*/
        $data = $this->request('/vpos/api/0.3/multi_buy/confirmations', $req);
       /* if ($data->status === 'success') {
            return array( 'url' => $this->url  . '/payment/multi_buy?process_id=' . $data->process_id, 'id' => $data->process_id);
        }*/
    }

    protected function request($url, Array $req)
    {
        $json = json_encode($req);
        $ch   = curl_init($this->url . $url);
        curl_setopt_array($ch, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_VERBOSE => 1,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json),
            ),
        ));
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }

    protected function token()
    {
        return md5( $this->private . implode( '', func_get_args()));
    }
}

$x = new GetConfirmations('0fyeIXx4B1bCgDBc4mb5bNmOr9YrrhvE', 'pi$BIONR.x9sZZ,HZj8YrBoU6x$4tO3zIgVg9a,+');
$buy = $x->staging()->buy($shop_process_id);

// guardar $bud['id'] en la DB.
exit;
