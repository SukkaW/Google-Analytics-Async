<?php
    header("status: 204");
    header("cache-control: no-cache, no-store, must-revalidate");
    $check_referer_domain=false;
    $domain='example.com';  //If you open the domain check set your domain here
    
    if (empty($_SERVER['HTTP_REFERER']) || empty($_SERVER['HTTP_USER_AGENT'])){
        echo 'error';
        die();
    }
    
    if ($check_referer_domain){
        $info = parse_url($_SERVER['HTTP_REFERER']);
        $local = isset($info['host']) ? $info['host'] : '';
        if ($local!=$domain){
            echo 'error';
            die();
        }
    }
    
    function create_uuid(){
        $str = md5(uniqid(mt_rand(), true));
        $uuid = substr($str,0,8) . '-';
        $uuid .= substr($str,8,4) . '-';
        $uuid .= substr($str,12,4) . '-';
        $uuid .= substr($str,16,4) . '-';
        $uuid .= substr($str,20,12);
        return $uuid;
    }
    if (!isset($_COOKIE["uuid"])) {
        $uuid=create_uuid();
        setcookie("uuid", $uuid , time()+368400000);
    } else {
        $uuid=$_COOKIE["uuid"];
    }
    if (function_exists("fastcgi_finish_request")) {
        fastcgi_finish_request();
    }

    ob_start();
    header("Connection: close"); 
    header('Content-Encoding: none');
    header('Content-Length: 0');
    ob_end_flush();
    flush();

    if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
        $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
    }

    $url='v=1&t=pageview&';
    $url.='tid='.rawurlencode(rawurldecode($_GET['tid'])).'&';
    $url.='cid='.$uuid.'&';
    $url.='dl='.rawurlencode(rawurldecode($_SERVER['HTTP_REFERER'])).'&';
    $url.='uip='.rawurlencode(rawurldecode($_SERVER['REMOTE_ADDR'])).'&';
    $url.='ua='.rawurlencode(rawurldecode($_SERVER['HTTP_USER_AGENT'])).'&';
    $url.='dt='.rawurlencode(rawurldecode($_GET['dt'])).'&';
    $url.='de='.rawurlencode(rawurldecode($_GET['de'])).'&';
    $url.='dr='.rawurlencode(rawurldecode($_GET['dr'])).'&';
    $url.='ul='.rawurlencode(rawurldecode($_GET['ul'])).'&';
    $url.='sd='.rawurlencode(rawurldecode($_GET['sd'])).'&';
    $url.='sr='.rawurlencode(rawurldecode($_GET['sr'])).'&';
    $url.='vp='.rawurlencode(rawurldecode($_GET['vp'])).'&';
    $url.='z='.$_GET['z'];
    $url='https://www.google-analytics.com/collect?'.$url;
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);

?>
