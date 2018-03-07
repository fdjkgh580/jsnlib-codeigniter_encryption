<?php 
namespace Jsnlib\Codeigniter;

class Encryption {

    protected $ci;

    function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->library('encryption');
    }

    /**
     * 加密
     * @param    $param['expiry'] 不指定將自動添加預設值
     */
    public function encrypt(array $param): string
    {
        $param += 
        [
            'expiry' => \Helper\date_before_after("Y-m-d H:i:s", "now", +30, "minute")
        ];

        $result = $this->ci->encryption->encrypt(json_encode($param));

        $json_decode = $this->ci->encryption->decrypt($result);

        if (empty($result)) throw new \Exception("config.php 參數 encryption_key 必須要設定");
        
        return base64_encode($result);
    } 

    /**
     * 解密，並自動判斷是否到期
     * @param   $encrypt 由 encrytion() 加密的字串
     * @return  [is_expired => 是否到期, data => 解密後的資料] 
     */
    public function decrypt(string $encrypt): array
    {
        $decode = base64_decode($encrypt);
        $json_decode = $this->ci->encryption->decrypt($decode);
        $ary = json_decode($json_decode, true);

        $is_expired = \Helper\iscurrent_expired($ary['expiry']);

        return 
        [
            'is_expired' => $is_expired,
            'data' => $ary
        ];
    }


}