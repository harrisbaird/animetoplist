<?php // /app/controllers/components/php_b_b3.php
/**
* Created by Willson Sheldon => http://bakery.cakephp.org/articles/view/phpbb3-api-bridge
* Modified by Òscar Casajuana a.k.a. elboletaire => http://www.underave.net
*/
class Phpbb3Component extends Object
{

    var $controller;
    var $model;
    var $phpBBpath;
    var $auth;
    var $user;
    var $passwordHash = '1t54sec1e0';

    public function initialize(&$controller)
    {
        $this->phpBBpath = WWW_ROOT . 'forum' . DS;

        $this->controller = &$controller;
        define('IN_PHPBB', true);
        define('PHPBB_INSTALLED', true);

        global $phpbb_root_path, $phpEx, $db, $config, $user, $auth, $cache, $template;

        $phpbb_root_path = $this->phpBBpath;
        $phpEx = substr(strrchr(__FILE__, '.'), 1);
        require_once($phpbb_root_path . 'common.' . $phpEx);

        $this->table_prefix = $table_prefix;
        $this->auth = $auth;
        $this->user = $user;

        // Start session management
        $this->user->session_begin();
        $this->auth->acl($user->data);
        $this->user->setup();

        require_once($phpbb_root_path .'includes/functions_user.php');
    }

    /**
     * Inicia la sesión en phpBB3
     * @param string $username
     * @param string $password
     * @param bool $remember [optional] Recordar entre sesiones
     */
    public function login($username, $email, $remember = false)
    {
        if(!$this->userExists($username)) {
            $data = array(
                'username' => $username,
                'user_password' => $username . $this->passwordHash,
                'user_email' => $email
            );

            $userid = $this->register($data);
        }


        $this->auth->login($username, $username . $this->passwordHash, $remember);
    }

    /**
     * Cierra la sesión en phpBB
     */
    public function logout()
    {
        $this->user->session_kill();
        $this->user->session_begin();
    }

    /**
     * Registra un usuario en el sistema
     * @param array $data Datos del usuario
     * @return id del usuario en caso de éxito; falso en caso contrario
     */
    public function register($data)
    {
        // Paràmetres per defecte
        // Grup usuaris registrats
        if(!isset($data['group_id']) || empty($data['group_id'])) $data['group_id'] = 2;
        if(!isset($data['user_type']) || empty($data['user_type'])) $data['user_type'] = 0;
        $userData = array(
            'username' => $data['username'],
            'username_clean' => strtolower($data['username']),
            'user_password' => $this->phpbb_hash($data['user_password']),
            'user_email' => $data['user_email'],
            'user_ip'=>$_SERVER['REMOTE_ADDR'],
            'group_id' => $data['group_id'], //Registered users group
            'user_type' => $data['user_type'],
        );
        $userId = user_add($userData);
        if(empty($userId)) return false;
        else
        {
            // Actualitzem darrer usuari registrat al phpBB
            update_last_username();
            return $userId;
        }
    }

    /**
     * Encripta una contraseña utilizando el
     * método de encriptación de phpBB3
     * @param string $password
     * @return contraseña encriptada
     */
    public function phpbb_hash($password)
    {

        $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $random_state = $this->unique_id();
        $random = '';
        $count = 6;

        if (($fh = @fopen ( '/dev/urandom', 'rb' )))
        {
            $random = fread ($fh, $count);
            fclose ($fh);
        }

        if (strlen($random) < $count)
        {
            $random = '';
            for($i=0;$i<$count;$i+=16)
            {
                $random_state = md5($this->unique_id () . $random_state);
                $random .= pack('H*', md5($random_state));
            }
            $random = substr($random, 0, $count);
        }

        $hash = $this->_hash_crypt_private($password, $this->_hash_gensalt_private($random, $itoa64 ), $itoa64);

        if (strlen($hash) == 34)
        {
            return $hash;
        }

        return md5($password);
    }

    /**
     * Verifica la existencia de un usuario
     * @param string $username
     * @return
     */
    public function userExists($username)
    {
        $idArray = false;
        if (user_get_id_name(&$idArray, $username) == 'NO_USERS')
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    private function unique_id($extra = 'c')
    {
        static $dss_seeded = false;
        global $config;

        $val = $config ['rand_seed'] . microtime ();
        $val = md5 ( $val );
        $config ['rand_seed'] = md5 ( $config ['rand_seed'] . $val . $extra );

        $dss_seeded = true;
        return substr ( $val, 4, 16 );
    }

    /**
     * Generate salt for hash generation
     */
    private function _hash_gensalt_private($input,&$itoa64,$iteration_count_log2 = 6)
    {
        if ($iteration_count_log2 < 4 || $iteration_count_log2 > 31)
        {
            $iteration_count_log2 = 8;
        }

        $output = '$H$';
        $output .= $itoa64 [min($iteration_count_log2 + ((PHP_VERSION >= 5) ? 5 : 3), 30)];
        $output .= $this->_hash_encode64($input, 6, $itoa64);

        return $output;
    }

    /**
     * Encode hash
     */
    private function _hash_encode64($input,$count,&$itoa64)
    {
        $output = '';
        $i = 0;
        do
        {
            $value = ord ( $input [$i ++] );
            $output .= $itoa64 [$value & 0x3f];
            if ($i < $count)
            {
                $value |= ord ( $input [$i] ) << 8;
            }
            $output .= $itoa64 [($value >> 6) & 0x3f];
            if ($i ++ >= $count)
            {
                break;
            }
            if ($i < $count)
            {
                $value |= ord ( $input [$i] ) << 16;
            }
            $output .= $itoa64 [($value >> 12) & 0x3f];
            if ($i ++ >= $count)
            {
                break;
            }
            $output .= $itoa64 [($value >> 18) & 0x3f];
        } while ( $i < $count );

        return $output;
    }

    /**
     * The crypt function/replacement
     */
    private function _hash_crypt_private($password,$setting,&$itoa64)
    {
        $output = '*';

        // Check for correct hash
        if (substr ( $setting, 0, 3 ) != '$H$')
        {
            return $output;
        }
        $count_log2 = strpos ( $itoa64, $setting [3] );
        if ($count_log2 < 7 || $count_log2 > 30)
        {
            return $output;
        }
        $count = 1 << $count_log2;
        $salt = substr ( $setting, 4, 8 );

        if (strlen ( $salt ) != 8)
        {
            return $output;
        }
        /**
         * We're kind of forced to use MD5 here since it's the only
         * cryptographic primitive available in all versions of PHP
         * currently in use.  To implement our own low-level crypto
         * in PHP would result in much worse performance and
         * consequently in lower iteration counts and hashes that are
         * quicker to crack (by non-PHP code).
         */
        if (PHP_VERSION >= 5)
        {
            $hash = md5 ( $salt . $password, true );
            do
            {
                $hash = md5 ( $hash . $password, true );
            } while ( -- $count );
        }
        else
        {
            $hash = pack ( 'H*', md5 ( $salt . $password ) );
            do
            {
                $hash = pack ( 'H*', md5 ( $hash . $password ) );
            } while ( -- $count );
        }
        $output = substr ( $setting, 0, 12 );
        $output .= $this->_hash_encode64 ( $hash, 16, $itoa64 );

        return $output;
    }
}
?>
