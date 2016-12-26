<?php namespace JULIET\Calendar\helper;
  class Hash {
     const HASH_METHOD = 'aes128';
     const PRIVATE_HASH = '8654D3D1FB1DDDB214EE5729689A3';

    public static function hash_player($user) {
      $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
      $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

      $d = openssl_encrypt ($user, SELF::HASH_METHOD, SELF::PRIVATE_HASH, 0, $iv);
      return bin2hex($iv).$d;
    }

    public static function un_hash_player($user_hash) {
      $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC) * 2;
      $iv = hex2bin(substr($user_hash, 0, $iv_size));
      $decryptedMessage = openssl_decrypt(substr($user_hash, $iv_size), SELF::HASH_METHOD, SELF::PRIVATE_HASH, 0, $iv);

      return $decryptedMessage;
    }

  }
?>
