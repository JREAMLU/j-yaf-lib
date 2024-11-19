<?php

namespace App\Lib;

class Crypto
{
    private $encryption_key; // = "0F10F6CB2F5369C14D14FA07BAD302267901240CC8C845DD2C645FBD149A11C9";
    private $validation_key; // = "C985085862F161091EEEFE30F7DC9D62";
    private $encrypt_method;

    public function __construct($encryption_key, $validation_key)
    {
        $this->encryption_key = hex2bin($encryption_key);
        $this->validation_key = hex2bin($validation_key);
        $this->encrypt_method = 'AES-256-CBC';
    }

    public function encryptCookie($data = '')
    {
        // 生成IV
        $iv_length = openssl_cipher_iv_length($this->encrypt_method);
        $iv = openssl_random_pseudo_bytes($iv_length, $strong);
        if (false === $iv && false === $strong) {
            return false;
        }

        // 占位符
        $padding = $iv_length - (strlen($data) % $iv_length);

        // 数据和占位符放一起, 存放iv
        $data .= str_repeat(chr($padding), $padding);

        // 加密 OPENSSL_RAW_DATA: 二进制
        $encrypted = openssl_encrypt($data, $this->encrypt_method, $this->encryption_key, OPENSSL_RAW_DATA, $iv);

        // 带上iv hash
        $hash_data = $iv . $encrypted;
        $hash = hash_hmac("sha256", $hash_data, $this->validation_key);
        $encrypted_data = bin2hex($iv) . bin2hex($encrypted) . substr($hash, 0, 16);

        return $encrypted_data;
    }

    public function decryptCookie($data = '')
    {
        if (empty($data)) {
            return false;
        }

        //check encryptData mod == 0
        if ((strlen($data) % 2) != 0) {
            return false;
        }

        $data = hex2bin($data);
        $iv_length = openssl_cipher_iv_length($this->encrypt_method);
        $hash_size = 8;
        $hash = bin2hex(substr($data, -$hash_size));
        $need_hash_data = substr($data, 0, strlen($data) - $hash_size);

        if ($hash != substr(hash_hmac("sha256", $need_hash_data, $this->validation_key), 0, 16)) {
            return false;
        }

        $iv = substr($data, 0, $iv_length);

        $_data = substr($data, $iv_length, strlen($data) - $iv_length - $hash_size);

        $decrypted_data = openssl_decrypt($_data, $this->encrypt_method, $this->encryption_key, OPENSSL_RAW_DATA, $iv);

        $padding = ord($decrypted_data[strlen($decrypted_data) - 1]);

        if ($padding > strlen($decrypted_data)) {
            return false;
        }
        if (strspn($decrypted_data, chr($padding), strlen($decrypted_data) - $padding) != $padding) {
            return false;
        }

        return substr($decrypted_data, 0, -1 * $padding);
    }

    public function genKey($length = 8)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }
}
