<?php
namespace Bancard;
$conn = pg_connect("host= port= dbname= user= password=");
class MultiBuy
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
        $this->url = 'https://vpos.infonet.com.py';
        return $this;
    }
    /** Aca tenes que mandar los datos en items, el producto y los precios
       Tenes que guardar el array('id') en tu DB y redireccionar hacia array('url');
    */
       public function buy($total_pyg = 0, $total_usd = 0, $total_items = 1)
       {
        session_start();
        $total_usd = number_format($total_usd, 2, ',', '');
        $total_pyg = number_format($primatotal, 2, ',', '');
        $shop_process_id = time();
        $token = $this->token($shop_process_id, $total_items, $total_usd, $total_pyg);
        $req   = array(
            'public_key' => $this->public,
            'operation' => array(
                'shop_process_id' => $shop_process_id,
                'return_url' => 'https://localhost/success.php',
                'cancel_url' => 'https://localhost/cancel.php',
                'token' => $token,
                'amount_in_us' => $total_usd,
                'amount_in_pyg' => $total_pyg,
                'additional_data' => '',
                'number_of_items' => 1,
                'items' => array(
                    array(
                        'amount' => $total_pyg,
                        'currency' => 'PYG',
                        'store' => 0000,
                        'store_branch' => 1,
                        'name' => 'nombreplan',
                        )
                    ),
                )
            );
        $_SESSION['process_id'] = $shop_process_id;
        $_SESSION['token'] = $token;
        $data = $this->request('/vpos/api/0.3/multi_buy', $req);
        if ($data->status === 'success') {
            return array( 'url' => $this->url  . '/payment/multi_buy?process_id=' . $data->process_id, 'id' => $data->process_id);
        }
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
$x = new MultiBuy('jJNt5AyL23EGXR3Rtx87RA8gnlYOpCBl', 'ld4jda2VpBxsp9ifwrkai1wE751GiGsOsVqLYxj8');
$buy = $x->staging()->buy($total_pyg);
// guardar $bud['id'] en la DB.
header("Location: " . $buy['url']);
exit;
