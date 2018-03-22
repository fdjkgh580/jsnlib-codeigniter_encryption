# Encryption

## 範例
````php
class Welcome extends CI_Controller {

    public function index()
    {
        // 1. 使用 encryption 類別
        $this->load->library('encryption');

        // 2. 初始化並在建構子置入 CI 原生的 encryption 物件
        $this->jsnlib_ecp = new \Jsnlib\Codeigniter\Encryption($this->encryption);
        
        // 3. 產生加密字串
        $token = $this->jsnlib_ecp->encrypt(
        [
            'name' => 'Jason',
            'age' => 18,
            'expiry' => '2019-01-01 10:00:00' //若不指定過期時間，將自動添加預設值
        ]);

        // 4. 解密字串
        $ary = $this->jsnlib_ecp->decrypt($token);
    }

}
````
## 說明
### encrypt(array $param): string
@param expiry 若不指定過期時間，將自動添加預設值  
  
@return 將加密過的資料以 base64 的格式輸出

### decrypt(string $encrypt): array
@param encrypt 加密的文字  
  
@return is_expired 是否已經過期
  
@return data 加密前的原始資料
