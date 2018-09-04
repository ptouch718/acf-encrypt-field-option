<?php
/*
Plugin Name: Advanced Custom Fields Encrypt Field Option
Plugin URI: https://github.com/ptouch718/acf-encrypt-field-option
Description: Adds an option to encrypt text field values upon save
Version: 1.0.0
Author: Powell May 
Author URI: powell.may@gmail.com
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

class acf_encrypt_field_option
{

    private $key;

    public function __construct(
        $key = 'keystring'
    )
    {
        $this->key = defined('ACF_ENCRYPT_FIELD_KEY') ? ACF_ENCRYPT_FIELD_KEY : $key;
        $this->initialize();
    }

    public function initialize()
    {
        add_action('acf/render_field_settings/type=text', [$this, 'render_field_settings'], 10, 3);
        add_action('acf/update_value', [$this, 'update_value'], 10, 3);
        add_action('acf/load_value', [$this, 'load_value'], 10, 3);
        add_filter('acf/prepare_field', [$this, 'prepare_field'], 10, 3);
    }

    public function render_field_settings($field)
    {
        acf_render_field_setting( $field, [
            'label'         => __('Encrypt Field?'),
            'instructions'  => '',
            'name'          => '_is_encrypted',
            'type'          => 'true_false',
            'ui'            => 1,
        ], true);   
    }

    public function update_value($value, $post_id, $field)
    {
        if ($field['_is_encrypted'])
        {
            return $this->encrypt($value, $this->key);
        }
        return $value;
    }

    public function load_value($value, $post_id, $field)
    {
        if ($field['_is_encrypted'])
        {
            return $this->decrypt($value, $this->key);
        }
        return $value;
    }

    public function prepare_field($field)
    {
        if ($field['_is_encrypted'])
        {
            $field_selector = '.acf-field-'.substr($field['key'], 6);
            ?>
            <style type="text/css">
                <?= $field_selector; ?> label:after{
                    content: " (encrypted)";
                    font-size: 80%;
                    font-weight: normal;
                    color: #CCC;
                }
                <?= $field_selector; ?> .acf-input-wrap input {
                    display: none;
                }
            </style>
            <script>
                (function () {
                    document.addEventListener('DOMContentLoaded', function () {
                        var inputWrapper = document.querySelector('<?= $field_selector; ?>');
                        var input = inputWrapper.querySelector('input')
                        var button = document.createElement('a');
                        button.href = '#';
                        button.innerHTML = (input.value)  ? 'Click to Show' : 'Click to Add';
                        button.className = 'acf-button button';
                        button.addEventListener('click', function (e) {
                            e.preventDefault();
                            this.style.display = 'none';
                            input.style.display = 'block';
                            return false;
                        });
                        inputWrapper.appendChild(button)
                    })
                }())
            </script>
            <?php
        }
        return $field;
    }

    private function encrypt($str, $key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $h_key = hash('sha256', $key, TRUE);
        return base64_encode(mcrypt_encrypt(
            MCRYPT_RIJNDAEL_256, 
            $h_key, 
            $str, 
            MCRYPT_MODE_ECB, 
            $iv
        ));
    } 

    private function decrypt($enc_str, $key)
    {
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $h_key = hash('sha256', $key, TRUE);
        return trim(mcrypt_decrypt(
            MCRYPT_RIJNDAEL_256, 
            $h_key, 
            base64_decode($enc_str),
             MCRYPT_MODE_ECB, 
             $iv
        ));
    }
}
new acf_encrypt_field_option();
