<?php
/**
 * Created by PhpStorm.
 * User: MikOnCode
 * Date: 28/10/2018
 * Time: 03:23
 */

function maybe_serialize($data)
{
    if (is_array($data) || is_object($data))
        return serialize($data);

    // Double serialization is required for backward compatibility.
    // See https://core.trac.wordpress.org/ticket/12930
    // Also the world will end. See WP 3.6.1.
    if (is_serialized($data, false))
        return serialize($data);

    return $data;
}

function user_can($group_name, $userID = null)
{
    $ci = &get_instance();
    return $ci->ion_auth->in_group($group_name, $userID);
}

/**
 * Unserialize value only if it was serialized.
 *
 * @since 2.0.0
 *
 * @param string $original Maybe unserialized original, if is needed.
 * @return mixed Unserialized data can be any type.
 */
function maybe_unserialize($original)
{
    if (is_serialized($original)) // don't attempt to unserialize data that wasn't serialized going in
        return @unserialize($original);
    return $original;
}

/**
 * Check value to find if it was serialized.
 *
 * If $data is not an string, then returned value will always be false.
 * Serialized data is always a string.
 *
 * @since 2.0.5
 *
 * @param string $data Value to check to see if was serialized.
 * @param bool $strict Optional. Whether to be strict about the end of the string. Default true.
 * @return bool False if not serialized and true if it was.
 */
function is_serialized($data, $strict = true)
{
    // if it isn't a string, it isn't serialized.
    if (!is_string($data)) {
        return false;
    }
    $data = trim($data);
    if ('N;' == $data) {
        return true;
    }
    if (strlen($data) < 4) {
        return false;
    }
    if (':' !== $data[1]) {
        return false;
    }
    if ($strict) {
        $lastc = substr($data, -1);
        if (';' !== $lastc && '}' !== $lastc) {
            return false;
        }
    } else {
        $semicolon = strpos($data, ';');
        $brace = strpos($data, '}');
        // Either ; or } must exist.
        if (false === $semicolon && false === $brace)
            return false;
        // But neither must be in the first X characters.
        if (false !== $semicolon && $semicolon < 3)
            return false;
        if (false !== $brace && $brace < 4)
            return false;
    }
    $token = $data[0];
    switch ($token) {
        case 's' :
            if ($strict) {
                if ('"' !== substr($data, -2, 1)) {
                    return false;
                }
            } elseif (false === strpos($data, '"')) {
                return false;
            }
        // or else fall through
        case 'a' :
        case 'O' :
            return (bool)preg_match("/^{$token}:[0-9]+:/s", $data);
        case 'b' :
        case 'i' :
        case 'd' :
            $end = $strict ? '$' : '';
            return (bool)preg_match("/^{$token}:[0-9.E-]+;$end/", $data);
    }
    return false;
}

function uploadBase64($base64)
{
    $image_parts = explode(";base64,", $base64);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_type = getExtensionByMime($image_type);
    $image_base64 = base64_decode($image_parts[1]);
    //echo json_encode($image_type);die();
// decoding base64 string value
    $ci = &get_instance();
    $ci->load->helper('path');
    $path = set_realpath('uploads/');
    $fileName = md5(uniqid()) . '.' . $image_type;
    $file = $path . $fileName;
    file_put_contents($file, $image_base64);
    return $fileName;
}

function getRawInput()
{
    $ci =& get_instance();
    return (array)json_decode($ci->input->raw_input_stream);
}

function getExtensionByMime($mime)
{
    $extensions = [
        'jpeg' => 'jpg',
        'png' => 'png',
    ];
    return $extensions[$mime];
}

function getImageExtensions()
{
    return [
        'jpeg' => 'jpg',
        'png' => 'png',
    ];
}

function maybe_null_or_empty($element, $property)
{
    if (is_object($element)) {
        $element = (array)$element;
    }
    if (isset($element[$property])) {
        return $element[$property];
    } else {
        return "";
    }
}

function get_meta($id, $key, $table_meta, $table_id_val)
{
    $ci =& get_instance();
    if (!empty($id) && !empty($key)) {
        $query = $ci->db->get_where($table_meta, array(
            $table_id_val => $id,
            'key' => $key,
        ))->row();
        $query = maybe_null_or_empty($query, 'value');
        return maybe_unserialize($query);
    }
}

function update_meta($id, $key, $value, $table_meta, $table_id_val)
{
    $ci =& get_instance();
    if (!empty($id) && !empty($key) /*&& !empty($value)*/) {
        $query = $ci->db->get_where($table_meta, array(
            $table_id_val => $id,
            'key' => $key,
        ));
        if (empty($query->row())) {
            $ci->db->insert($table_meta, array(
                $table_id_val => $id,
                'key' => $key,
                'value' => $value
            ));
        } else {
            $ci->db->where(array(
                $table_id_val => $id,
                'key' => $key,
            ));
            $ci->db->update($table_meta, array('value' => maybe_serialize($value)));
        }
    }
}

function getDateByTime($time, $dateFormat = 'd/m/Y')
{
    $time = (int)$time;
    return date($dateFormat, $time);
}

function echoResponse($data)
{
    echo json_encode($data);
    die();
}

function sendMail($default = 'feminit@solidarit-hub.org', $args)
{
    $default = 'feminit@solidarit-hub.org';
    ini_set("SMTP", "smtp.solidarit-hub.org");
    $ci =& get_instance();
    $options = $ci->option_model->get_options();
    $message = mailTemplateHtml($args, $options);
    $headers = "MIME-Version: 1.0 \n";

    $headers .= "Content-type: text/html; charset=iso-8859-1 \n";

    $headers .= "From: $default  \n";

    $headers .= "Disposition-Notification-To: $default  \n";
    $headers .= "X-Priority: 1  \n";

    $headers .= "X-MSMail-Priority: High \n";
    @mail($args['destination'], $args['title'], $message, $headers);

    //echoResponse($ci->email->print_debugger());
//    var_dump($args['destination']);exit;
    //var_dump();exit;
}

function get_upload_path($additional = "uploads/")
{
    return base_url($additional);
}

function mailTemplateHtml($args, $options)
{
    $path = get_upload_path();
    ob_start();
    ?>

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
            "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml"
          style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">

    <!-- Mirrored from coderthemes.com/highdmin/horizontal/email-templates/action.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 03 Sep 2018 12:38:14 GMT -->
    <head>
        <meta name="viewport" content="width=device-width"/>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title><?php echo $args['title'] ?></title>


        <style type="text/css">
            img {
                max-width: 100%;
            }

            body {
                -webkit-font-smoothing: antialiased;
                -webkit-text-size-adjust: none;
                width: 100% !important;
                height: 100%;
                line-height: 1.6em;
            }

            body {
                background-color: #f6f6f6;
            }

            @media only screen and (max-width: 640px) {
                body {
                    padding: 0 !important;
                }

                h1 {
                    font-weight: 800 !important;
                    margin: 20px 0 5px !important;
                }

                h2 {
                    font-weight: 800 !important;
                    margin: 20px 0 5px !important;
                }

                h3 {
                    font-weight: 800 !important;
                    margin: 20px 0 5px !important;
                }

                h4 {
                    font-weight: 800 !important;
                    margin: 20px 0 5px !important;
                }

                h1 {
                    font-size: 22px !important;
                }

                h2 {
                    font-size: 18px !important;
                }

                h3 {
                    font-size: 16px !important;
                }

                .container {
                    padding: 0 !important;
                    width: 100% !important;
                }

                .content {
                    padding: 0 !important;
                }

                .content-wrap {
                    padding: 10px !important;
                }

                .invoice {
                    width: 100% !important;
                }
            }
        </style>
    </head>

    <body style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; -webkit-font-smoothing: antialiased; -webkit-text-size-adjust: none; width: 100% !important; height: 100%; line-height: 1.6em; background-color: #f6f6f6; margin: 0;"
          bgcolor="#f6f6f6">

    <table class="body-wrap"
           style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: #f6f6f6; margin: 0;"
           bgcolor="#f6f6f6">
        <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
            <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;"
                valign="top"></td>
            <td class="container" width="600"
                style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;"
                valign="top">
                <div class="content"
                     style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                    <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope
                           itemtype="http://schema.org/ConfirmAction"
                           style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; margin: 0; border: none;"
                    >
                        <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                            <td class="content-wrap"
                                style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;padding: 30px;border: 3px solid #777edd;border-radius: 7px; background-color: #fff;"
                                valign="top">
                                <meta itemprop="name" content="Confirm Email"
                                      style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;"/>
                                <table width="100%" cellpadding="0" cellspacing="0"
                                       style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                    <tr>
                                        <td style="text-align: center">
                                            <a href="#" style="display: block;margin-bottom: 10px;"> <img
                                                        src="<?= $path . $options['siteLogo'] ?>" height="50"
                                                        alt="logo"/></a> <br/>
                                        </td>
                                    </tr>
                                    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <td class="content-block"
                                            style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                            valign="top">
                                            <?php echo $args['message'] ?>
                                        </td>
                                    </tr>
                                    <?php
                                    if (isset($args['btnLink']) && isset($args['btnLabel'])) {
                                        ?>
                                        <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <td class="content-block" itemprop="handler" itemscope
                                                itemtype="http://schema.org/HttpActionHandler"
                                                style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                                valign="top">
                                                <a target="_blank" href="<?= $args['btnLink'] ?>" class="btn-primary"
                                                   itemprop="url"
                                                   style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #02c0ce; margin: 0; border-color: #02c0ce; border-style: solid; border-width: 8px 16px;"><?= $args['btnLabel'] ?></a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>


                                    <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                        <td class="content-block"
                                            style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;"
                                            valign="top">
                                            &mdash; <b>Cordialement</b> - Equipe <?php echo $options['siteName'] ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                    <div class="footer"
                         style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; clear: both; color: #999; margin: 0; padding: 20px;">
                        <table width="100%"
                               style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                            <tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                <td class="aligncenter content-block"
                                    style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 12px; vertical-align: top; color: #999; text-align: center; margin: 0; padding: 0 0 20px;"
                                    align="center" valign="top"><?php echo $options['siteDescription'] ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </td>
            <td style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;"
                valign="top"></td>
        </tr>
    </table>
    </body>

    <!-- Mirrored from coderthemes.com/highdmin/horizontal/email-templates/action.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 03 Sep 2018 12:38:14 GMT -->
    </html>


    <?php
    return ob_get_clean();
}

function custom_send($json, $page_access_token)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://graph.facebook.com/v2.6/me/messenger_profile?access_token=" . $page_access_token);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($json)));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $ret = curl_exec($ch);
    var_dump_pre($ret);
}

function setFormValidationRules($data)
{

    if (!empty($data)) {
        $CI =& get_instance();
        foreach ($data as $datum) {
            $CI->form_validation->set_rules($datum['name'], $datum['label'], $datum['rules']);
        }
    }
}

function setErrorDelimiter(){
    $CI = &get_instance();
    $CI->form_validation->set_error_delimiters(null, null);
    return $CI->form_validation->error_string();
}

function control_unique_on_update($value, $db_field)
{
    $ci = &get_instance();
    $db_field = explode('.', $db_field);
    $table = $db_field[0];
    $target_field = $db_field[1];
    $id_value = $db_field[2];
    $query = $ci->db->query("SELECT id FROM $table where $target_field='$value'")->row();
    if ($queryId = maybe_null_or_empty($query, 'id')) {
        if ($id_value != $queryId) {
            $ci->form_validation->set_message('is_unique_on_update', "{field} existe dejà");
            return false;
        }
    }
    return true;
}
function getConfig()
{
    return array(
        'page_access_token' => 'EAAcjoBipBNsBAI8bM7F1UiNzRoFmolbzofZAdtgPYV6jFCmd0pgp7ZCUGNLUtPtCdkCnKiRVzvGGTvwXsCYKXUOidkswoM2jAyDM5bR6RfuXeZBDTZCuqOM4BXPE8oOZC16rZCYwalUqjkUkBSx8N6a69s46Nay4cCFNAr0QL2lmIxwpk8vu2o',
        'verify_token' => 'ifisun2018',
        'debug' => false,
    );
}

function var_dump_pre($dump)
{
    echo '<pre>';
    var_dump($dump);
    echo '</pre>';
}

function saveFile($url, $dir)
{
    $ci = &get_instance();
    $ci->load->helper('path');
    $dir = set_realpath($dir);
    $urlB = $url;
    //remove the query string and get the file name
    if ($url = parse_url($url)) {
        $cleanUrl = $url['scheme'] . $url['host'] . $url['path'];
    }
    //get the pathinfo() of the url
    $cleanUrl = pathinfo($cleanUrl);
    //get the file name
    $name = $cleanUrl['basename'];
    //check if the directory exists and create a new directory if it does not
    if (!file_exists($dir)) {
        mkdir($dir);
    }
    //check if the file exists and prepend a timestamp to its name if it does
    if (file_exists(/*dirname(__FILE__) . '/' .*/
        $dir . '/' . $name)) {
        $name = time() . "-" . $name;
    }

    //create a new file where its contents will be dumped
    $fp = fopen(/*dirname(__FILE__) . '/' .*/
        $dir . '/' . $name, 'w+');

    //Here is the file we are downloading, replace spaces with %20
    $ch = curl_init(str_replace(" ", "%20", $urlB));

    curl_setopt($ch, CURLOPT_TIMEOUT, 50);
    //disable ssl cert verification to allow copying files from HTTPS NB: you can always fix your php 'curl.cainfo' setting so yo dont have to disable this
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // write curl response to file
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // get curl response
    $exec = curl_exec($ch);

    curl_close($ch);
    fclose($fp);
    if ($exec == true) {
        $returnData[0] = true;
    } else {
        $returnData[0] = false;
    }

    $returnData[1] = $dir;
    $returnData[2] = $url;
    $returnData[3] = $name;
    $returnData[4] = $dir . '/' . $name;
    return $name;
}

function validate_phone_number($phone)
{
    // Allow +, - and . in phone number
    $filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
    // Remove "-" from number
    $phone_to_check = str_replace("-", "", $filtered_phone_number);
    // Check the lenght of number
    // This can be customized if you want phone number from a specific country
    if (strlen($phone_to_check) < 8 || strlen($phone_to_check) > 16) {
        return false;
    } else {
        return true;
    }
}

function upload_data($args, $name, $resize = false)
{
    $args['encrypt_name'] = true;
//    var_dump($args);exit;
    $ci =& get_instance();
    $ci->load->library('upload', $args);
    if ($ci->upload->do_upload($name)) {
        return $ci->upload->data();
    }
    return $ci->upload->display_errors('', '');
    /*if (!empty($names)) {
        foreach ($names as $name) {
            if ($ci->upload->do_upload($name)) {
                $data[$name] = $ci->upload->data();
            }else{
                $data[$name]=$ci->upload->display_errors('', '');
            }
        }
    }*/
    /*if (!empty($names)) {
        foreach ($names as $name) {
            if ($ci->upload->do_upload($name)) {
                $data[$name] = $ci->upload->data();
                if ($resize) {
                    $config2 = [];
                    $config2['image_library'] = 'gd2';
                    $config2['source_image'] = $data[$name]['full_path'];
//                $config2['new_image'] = './image_uploads/thumbs';
                    $config2['maintain_ratio'] = false;
                    $config2['width'] = 512;
                    $config2['height'] = 512;
                    $ci->load->library('image_lib', $config2);
                    $ci->image_lib->resize();
                }
            }
        }
        return $data;
    }*/
}