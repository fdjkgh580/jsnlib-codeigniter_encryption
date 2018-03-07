<?php 
namespace Jsnlib\Codeigniter;

class Encryption {

    protected $ci;
    protected $ci_encryption;

    /**
     * 建構子
     * @param $ci_encryption 須要 Codeigniter 的 Encryption 物件
     */
    function __construct(\CI_Encryption $ci_encryption)
    {
        $this->ci_encryption = $ci_encryption;
    }

    /**
     * 加密
     * @param    $param['expiry'] 不指定將自動添加預設值
     */
    public function encrypt(array $param): string
    {
        $param += 
        [
            'expiry' => $this->date_before_after("Y-m-d H:i:s", "now", +30, "minute")
        ];

        $result      = $this->ci_encryption->encrypt(json_encode($param));
        
        $json_decode = $this->ci_encryption->decrypt($result);
        
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
        $decode      = base64_decode($encrypt);
        $json_decode = $this->ci_encryption->decrypt($decode);
        $ary         = json_decode($json_decode, true);
        $is_expired  = $this->iscurrent_expired($ary['expiry']);

        return 
        [
            'is_expired' => $is_expired,
            'data' => $ary
        ];
    }

    /**
     * 取得多少天前後的日期格式
     * 如 date_before_after("Y-m-d H:i:s", "now", +1, "day");
     * 可取得明天的日期
     * 
     * @param  $date_format 日期格式如 "Y-m-d H:i:s"
     * @param  $use_date    基準日期 可用now代表今天, 或使用 date() 字串
     * @param  $limit_num   多少天
     * @param  $type        參考 strtotime 的類型 如天數 day
     * @return              $date_format 指定的日期字串       
     */
    private function date_before_after($date_format, $use_date, $limit_num, $type): string
    {
        return date($date_format, strtotime("{$use_date} {$limit_num} {$type}"));
    }

    /**
     * 當前時間，相對於指定時間，是否過期了？
     * @param   $limit_time  date()的格式如 '2014-12-25 00:00:00'
     * @return  bool
     */
    private function iscurrent_expired($limit_time): bool
    {
        return (time() >= strtotime($limit_time)) ? true : false;
    }
}
