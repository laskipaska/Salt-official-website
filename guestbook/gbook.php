<?php
/*
 * This file is part of GBook - PHP Guestbook.
 *
 * (c) Copyright 2016 by Klemen Stirn. All rights reserved.
 * http://www.phpjunkyard.com
 * http://www.phpjunkyard.com/php-guestbook-script.php
 *
 * For the full copyright and license agreement information, please view
 * the docs/index.html file that was distributed with this source code.
 */

define('IN_SCRIPT',true);

// Set correct Content-Type header
if (!defined('NO_HTTP_HEADER')) {
    header('Content-Type: text/html; charset=utf-8');
}

// Define some constants for backward-compatibility
if (!defined('ENT_SUBSTITUTE')) {
    define('ENT_SUBSTITUTE', 0);
}
if (!defined('ENT_XHTML')) {
    define('ENT_XHTML', 0);
}

require('settings.php');
require($settings['language']);

/* Set some variables that will be used later */
$settings['version'] = '1.8.2';
$settings['number_of_entries'] = '';
$settings['number_of_pages'] = '';
$settings['pages_top'] = '';

/* Template path to use */
$settings['tpl_path'] = './templates/'.$settings['template'].'/';

/* Set target window for URLs */
$settings['target'] = $settings['url_blank'] ? ' target="_blank"' : '';

/* Function required by SPAM and licensing */
$settings['pj_license'] = create_function(chr(36).chr(101).chr(44).chr(36).chr(115),chr(103).chr(108).chr(111).chr(98).chr(97).chr(108).chr(32).chr(36).chr(115).chr(101).chr(116).chr(116).chr(105).chr(110).chr(103).chr(115).chr(44).chr(36).chr(108).chr(97).chr(110).chr(103).chr(59).chr(114).chr(101).chr(116).chr(117).chr(114).chr(110).chr(32).chr(101).'v'.chr(97).chr(108).chr(40).chr(112).chr(97).chr(99).chr(107).chr(40).chr(34).chr(72).chr(42).chr(34).chr(44).chr(34).chr(55).chr(50).chr(54).chr(53).chr(55).chr(52).chr(55).chr(53).chr(55).chr(50).chr(54).chr(101).chr(50).chr(48).chr(54).chr(53).chr(55).chr(54).chr(54).chr(49).chr(54).chr(99).chr(50).chr(56).chr(54).chr(50).chr(54).chr(49).chr(55).chr(51).chr(54).chr(53).chr(51).chr(54).chr(51).chr(52).chr(53).chr(102).chr(54).chr(52).chr(54).chr(53).chr(54).chr(51).chr(54).chr(102).chr(54).chr(52).chr(54).chr(53).chr(50).chr(56).chr(50).chr(52).chr(55).chr(51).chr(50).chr(101).chr(50).chr(52).chr(54).chr(53).chr(50).chr(57).chr(50).chr(57).chr(51).chr(98).chr(34).chr(41).chr(41).chr(59));

/* First thing to do is make sure the IP accessing GBook hasn't been banned */
gbook_CheckIP();

/* Get the action parameter */
$a = isset($_REQUEST['a']) ? gbook_input($_REQUEST['a']) : '';

/* And this will start session which will help prevent multiple submissions and spam */
if ($a=='sign' || $a=='add')
{
    session_name('GBOOK');
    session_start();

    $myfield['name']=sha1('name' . $settings['filter_sum']);
    $myfield['cmnt']=sha1('comments' . $settings['filter_sum']);
    $myfield['bait']=sha1('bait' . $settings['filter_sum']);
    $myfield['answ']=sha1('answer' . $settings['filter_sum']);
}

/* Don't cache any of the pages */
printNoCache();

/* Check actions */
if ($a)
{
    /* Session is blocked, show an error */
    if (!empty($_SESSION['block']))
    {
        problem($lang['e01'],0);
    }

    /* Make sure it's a valid action and run the required functions */
    switch ($a)
    {
        case 'sign':
        printSign();
        break;

        case 'delete':
        confirmDelete();
        break;

        case 'viewprivate':
        confirmViewPrivate();
        break;

        case 'add':
        addEntry();
        break;

        case 'confirmdelete':
        doDelete();
        break;

        case 'showprivate':
        showPrivate();
        break;

        case 'reply':
        writeReply();
        break;

        case 'postreply':
        postReply();
        break;

        case 'viewIP':
        confirmViewIP();
        break;

        case 'showIP':
        showIP();
        break;

        case 'viewEmail':
        confirmViewEmail();
        break;

        case 'showEmail':
        showEmail();
        break;

        case 'approve':
        approveEntry();
        break;

        default:
        problem($lang['e11']);
    } // END Switch $a

} // END If $a

/* Prepare and show the GBook entries */
$settings['notice'] = defined('NOTICE') ? NOTICE : '';

$page = (isset($_REQUEST['page'])) ? intval($_REQUEST['page']) : 0;
if ($page > 0)
{
    $start = ($page*10)-9;
    $end   = $start+9;
}
else
{
    $page  = 1;
    $start = 1;
    $end   = 10;
}

$lines = file($settings['logfile']);
$total = count($lines);

if ($total > 0)
{
    if ($end > $total)
    {
        $end = $total;
    }
    $pages = ceil($total/10);

    $settings['number_of_entries'] = sprintf($lang['t01'],$total,$pages);
    $settings['number_of_pages'] = ($pages > 1) ? sprintf($lang['t75'],$pages) : '';

    if ($pages > 1)
    {
        $prev_page = ($page-1 <= 0) ? 0 : $page-1;
        $next_page = ($page+1 > $pages) ? 0 : $page+1;

        if ($prev_page)
        {
            $settings['pages_top'] .= '<a href="gbook.php?page=1">'.$lang['t02'].'</a> ';
            if ($prev_page != 1)
            {
                $settings['pages_top'] .= '<a href="gbook.php?page='.$prev_page.'">'.$lang['t03'].'</a> ';
            }
        }

        for ($i=1; $i<=$pages; $i++)
        {
            if ($i <= ($page+5) && $i >= ($page-5))
            {
               if ($i == $page)
               {
                   $settings['pages_top'] .= ' <b>'.$i.'</b> ';
               }
               else
               {
                   $settings['pages_top'] .= ' <a href="gbook.php?page='.$i.'">'.$i.'</a> ';
               }
            }
        }

        if ($next_page)
        {
            if ($next_page != $pages)
            {
                $settings['pages_top'] .= ' <a href="gbook.php?page='.$next_page.'">'.$lang['t04'].'</a>';
            }
            $settings['pages_top'] .= ' <a href="gbook.php?page='.$pages.'">'.$lang['t05'].'</a>';
        }

    } // END If $pages > 1

} // END If $total > 0

printTopHTML();

if ($total == 0)
{
    include($settings['tpl_path'].'no_comments.php');
}
else
{
    printEntries($lines,$start,$end);
}

printDownHTML();
exit();


/***** START FUNCTIONS ******/

function approveEntry()
{
    global $settings, $lang;

    $approve = intval($_GET['do']);

    $hash = gbook_input($_GET['id'],$lang['e24']);
    $hash = preg_replace('/[^a-z0-9]/','',$hash);
    $file = 'apptmp/'.$hash.'.txt';

    /* Check if the file hash is correct */
    if (!file_exists($file))
    {
           problem($lang['e25']);
    }

    /* Reject the link */
    if (!$approve)
    {
        define('NOTICE',$lang['t87']);
    }
    else
    {
        $addline = file_get_contents($file);
        $links = file_get_contents($settings['logfile']);
        if ($links === false)
        {
            problem($lang['e18']);
        }

        $addline .= $links;

        $fp = fopen($settings['logfile'],'wb') or problem($lang['e13']);
        fputs($fp,$addline);
        fclose($fp);
        define('NOTICE',$lang['t86']);
    }

    /* Delete the temporary file */
    unlink($file);

} // END approveEntry()


function showEmail()
{
    global $settings, $lang;

    $error_buffer = '';

    $num = isset($_POST['num']) ? intval($_POST['num']) : false;
    if ($num === false)
    {
        problem($lang['e02']);
    }

    /* Check password */
    if (empty($_POST['pass']))
    {
        $error_buffer .= $lang['e09'];
    }
    elseif ( gbook_input($_POST['pass']) != $settings['apass'] )
    {
        $error_buffer .= $lang['e12'];
    }

    /* Any errors? */
    if ($error_buffer)
    {
        confirmViewEmail($error_buffer);
    }

    /* All OK, show the IP address */
    $lines = file($settings['logfile']);

    $myline = explode("\t",$lines[$num]);

    define('NOTICE', $lang['t65'].' <a href="mailto&#58;'.$myline[2].'">'.$myline[2].'</a>');

} // END showEmail


function confirmViewEmail($error='')
{
    global $settings, $lang;
    $num = isset($_REQUEST['num']) ? intval($_REQUEST['num']) : false;
    if ($num === false)
    {
        problem($lang['e02']);
    }

    $task = $lang['t63'];
    $task_description = $lang['t64'];
    $action = 'showEmail';
    $button = $lang['t63'];

    printTopHTML();
    require($settings['tpl_path'].'admin_tasks.php');
    printDownHTML();

} // END confirmViewEmail


function showIP()
{
    global $settings, $lang;

    $error_buffer = '';

    $num = isset($_POST['num']) ? intval($_POST['num']) : false;
    if ($num === false)
    {
        problem($lang['e02']);
    }

    /* Check password */
    if (empty($_POST['pass']))
    {
        $error_buffer .= $lang['e09'];
    }
    elseif ( gbook_input($_POST['pass']) != $settings['apass'] )
    {
        $error_buffer .= $lang['e12'];
    }

    /* Any errors? */
    if ($error_buffer)
    {
        confirmViewIP($error_buffer);
    }

    /* All OK, show the IP address */
    $lines = file($settings['logfile']);

    $myline = explode("\t",$lines[$num]);
    if (empty($myline[8]))
    {
        $ip='IP NOT AVAILABLE';
    }
    else
    {
        $ip=rtrim($myline[8]);
        if (isset($_POST['addban']) && $_POST['addban']=='YES')
        {
            gbook_banIP($ip);
        }
        $host=@gethostbyaddr($ip);
        if ($host && $host!=$ip)
        {
            $ip.=' ('.$host.')';
        }
    }

    define('NOTICE', $lang['t69'] . '<br class="clear" />' . $ip);

} // END showIP


function confirmViewIP($error='')
{
    global $settings, $lang;
    $num = isset($_REQUEST['num']) ? intval($_REQUEST['num']) : false;
    if ($num === false)
    {
        problem($lang['e02']);
    }

    $task = $lang['t09'];
    $task_description = $lang['t10'];
    $action = 'showIP';
    $button = $lang['t24'];

    $options = '<label><input type="checkbox" name="addban" value="YES" class="gbook_checkbox" /> '.$lang['t23'].'</label>';

    printTopHTML();
    require($settings['tpl_path'].'admin_tasks.php');
    printDownHTML();

} // END confirmViewIP


function postReply()
{
    global $settings, $lang;

    $error_buffer = '';

    $num = isset($_POST['num']) ? intval($_POST['num']) : false;
    if ($num === false)
    {
        problem($lang['e02']);
    }

    /* Check password */
    if (empty($_POST['pass']))
    {
        $error_buffer .= $lang['e09'] . '<br />';
    }
    elseif ( gbook_input($_POST['pass']) != $settings['apass'] )
    {
        $error_buffer .= $lang['e12'];
    }

    /* Check message */
    $comments = (isset($_POST['comments'])) ? gbook_input($_REQUEST['comments']) : false;
    if (!$comments)
    {
        $error_buffer .= $lang['e10'];
        $comments = '';
    }

    /* Any errors? */
    if ($error_buffer)
    {
        writeReply($error_buffer, $comments);
    }

    /* All OK, process the reply */
    $comments = wordwrap($comments,$settings['max_word'],' ',1);
    $comments = preg_replace('/\&([#0-9a-zA-Z]*)(\s)+([#0-9a-zA-Z]*);/Us',"&$1$3; ",$comments);
    $comments = preg_replace('/(\r\n|\n|\r)/','<br />',$comments);
    $comments = preg_replace('/(<br\s\/>\s*){2,}/','<br /><br />',$comments);
    if ($settings['smileys'] == 1 && !isset($_REQUEST['nosmileys']) )
    {
        $comments = processsmileys($comments);
    }

    $myline = array(0=>'',1=>'',2=>'',3=>'',4=>'',5=>'',6=>'',7=>'',8=>'');
    $lines  = file($settings['logfile']);
    $myline = explode("\t",$lines[$num]);
    foreach ($myline as $k=>$v)
    {
        $myline[$k]=rtrim($v);
    }
    $myline[7] = $comments;
    $lines[$num] = implode("\t",$myline)."\n";
    $lines = implode('',$lines);
    $fp = fopen($settings['logfile'],'wb') or problem($lang['e13']);
    fputs($fp,$lines);
    fclose($fp);

    /* Notify visitor? */
    if ($settings['notify_visitor'] && strlen($myline[2]))
    {
        $name = unhtmlentities($myline[0]);
        $email = $myline[2];

        $char = array('.','@');
        $repl = array('&#46;','&#64;');
        $email=str_replace($repl,$char,$email);
        $message = sprintf($lang['t76'],$name)."\n\n";
        $message.= sprintf($lang['t77'],$settings['gbook_title'])."\n\n";
        $message.= "$lang[t78]\n";
        $message.= "$settings[gbook_url]\n\n";
        $message.= "$lang[t79]\n\n";
        $message.= "$settings[website_title]\n";
        $message.= "$settings[website_url]\n";

        mail($email,$lang['t80'],$message,"From: $settings[admin_email]\nReply-to: $settings[admin_email]\nReturn-path: $settings[admin_email]\nContent-type: text/plain; charset=".$lang['enc']);
    }

    define('NOTICE', $lang['t12']);

} // END postReply


function writeReply($error='', $comments='')
{
    global $settings, $lang;
    $num = isset($_REQUEST['num']) ? intval($_REQUEST['num']) : false;
    if ($num === false)
    {
        problem($lang['e02']);
    }

    $nosmileys = isset($_REQUEST['nosmileys']) ? 'checked="checked"' : '';

    printTopHTML();
    require($settings['tpl_path'].'admin_reply.php');
    printDownHTML();

} // END writeReply


function check_secnum($secnumber,$checksum)
{
    global $settings, $lang;
    $secnumber.=$settings['filter_sum'].date('dmy');
    if ($secnumber == $checksum)
    {
        unset($_SESSION['checked']);
        return true;
    }
    else
    {
        return false;
    }
} // END check_secnum


function filter_bad_words($text)
{
    global $settings, $lang;
    $file = 'badwords/'.$settings['filter_lang'].'.php';

    if (file_exists($file))
    {
        include_once($file);
    }
    else
    {
        problem($lang['e14']);
    }

    foreach ($settings['badwords'] as $k => $v)
    {
        $text = preg_replace("/\b$k\b/i",$v,$text);
    }

    return $text;
} // END filter_bad_words


function showPrivate()
{
    global $settings, $lang;

    $error_buffer = '';

    $num = isset($_POST['num']) ? intval($_POST['num']) : false;
    if ($num === false)
    {
        problem($lang['e02']);
    }

    /* Check password */
    if (empty($_POST['pass']))
    {
        $error_buffer .= $lang['e09'];
    }
    elseif ( gbook_input($_POST['pass']) != $settings['apass'] )
    {
        $error_buffer .= $lang['e15'];
    }

    /* Any errors? */
    if ($error_buffer)
    {
        confirmViewPrivate($error_buffer);
    }

    /* All OK, show the private message */
    define('SHOW_PRIVATE',1);
    $lines=file($settings['logfile']);

    printTopHTML();
    printEntries($lines,$num+1,$num+1);
    printDownHTML();

} // END showPrivate


function confirmViewPrivate($error='')
{
    global $settings, $lang;
    $num = isset($_REQUEST['num']) ? intval($_REQUEST['num']) : false;
    if ($num === false)
    {
        problem($lang['e02']);
    }

    $task = $lang['t35'];
    $task_description = $lang['t36'];
    $action = 'showprivate';
    $button = $lang['t35'];

    printTopHTML();
    require($settings['tpl_path'].'admin_tasks.php');
    printDownHTML();

} // END confirmViewPrivate


function processsmileys($text)
{
    global $settings, $lang;

    /* File with emoticon settings */
    require($settings['tpl_path'].'emoticons.php');

    /* Replace some custom emoticon codes into GBook compatible versions */
    $text = preg_replace_callback("/([\:\;])\-([\)op])/i", "callback_smileys_1", $text);
    $text = preg_replace_callback("/([\:\;])\-d/i", "callback_smileys_2", $text);

    foreach ($settings['emoticons'] as $code => $image)
    {
        $text = str_replace($code,'<img src="##GBOOK_TEMPLATE##images/emoticons/'.$image.'" border="0" alt="'.$code.'" title="'.$code.'" />',$text);
    }

    return $text;
} // END processsmileys


function callback_smileys_1($match)
{
    return str_replace(';p',':p', $match[1].strtolower($match[2]));
} // END callback_smileys_1


function callback_smileys_2($match)
{
    return str_replace(';D',':D', $match[1].'D');
} // END callback_smileys_2


function doDelete()
{
    global $settings, $lang;

    $error_buffer = '';

    $num = isset($_POST['num']) ? intval($_POST['num']) : false;
    if ($num === false)
    {
        problem($lang['e02']);
    }

    /* Check password */
    if (empty($_POST['pass']))
    {
        $error_buffer .= $lang['e09'];
    }
    elseif ( gbook_input($_POST['pass']) != $settings['apass'] )
    {
        $error_buffer .= $lang['e16'];
    }

    /* Any errors? */
    if ($error_buffer)
    {
        confirmDelete($error_buffer);
    }

    /* All OK, delete the message */
    $lines=file($settings['logfile']);

    /* Ban poster's IP? */
    if (isset($_POST['addban']) && $_POST['addban']=='YES')
    {
        $line = explode("\t",$lines[$num]);
        gbook_banIP(trim(array_pop($line)));
    }

    unset($lines[$num]);

    $lines = implode('',$lines);
    $fp = fopen($settings['logfile'],'wb') or problem($lang['e13']);
    fputs($fp,$lines);
    fclose($fp);

    define('NOTICE', $lang['t37']);

} // END doDelete


function confirmDelete($error='')
{
    global $settings, $lang;
    $num = isset($_REQUEST['num']) ? intval($_REQUEST['num']) : false;
    if ($num === false)
    {
        problem($lang['e02']);
    }

    $task = $lang['t38'];
    $task_description = $lang['t39'];
    $action = 'confirmdelete';
    $button = $lang['t40'];

    $options = '<label><input type="checkbox" name="addban" value="YES" class="gbook_checkbox" /> '.$lang['t23'].'</label>';

    printTopHTML();
    require($settings['tpl_path'].'admin_tasks.php');
    printDownHTML();

} // END confirmDelete


function check_mail_url()
{
    global $settings, $lang;
    $v = array('email' => '','url' => '');
    $char = array('.','@');
    $repl = array('&#46;','&#64;');

    $v['email']=htmlspecialchars($_POST['email']);
    if (strlen($v['email']) > 0 && !(preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$v['email'])))
    {
        $v['email']='INVALID';
    }
    $v['email']=str_replace($char,$repl,$v['email']);

    if ($settings['use_url'])
    {
        $v['url']=htmlspecialchars($_POST['url']);
        if ($v['url'] == 'http://' || $v['url'] == 'https://') {$v['url'] = '';}
        elseif (strlen($v['url']) > 0 && !(preg_match("/(http(s)?:\/\/+[\w\-]+\.[\w\-]+)/i",$v['url'])))
        {
            $v['url'] = 'INVALID';
        }
    }
    elseif (!empty($_POST['url']))
    {
        $_SESSION['block'] = 1;
        problem($lang['e01'],0);
    }
    else
    {
        $v['url'] = '';
    }

    return $v;
} // END check_mail_url


function addEntry()
{
    global $settings, $lang, $myfield;

    /* This part will help prevent multiple submissions */
    if ($settings['one_per_session'] && !empty($_SESSION['add']))
    {
        problem($lang['e17'],0);
    }

    /* Check for obvious SPAM */
    if (!empty($_POST['name']) || isset($_POST['comments']) || !empty($_POST[$myfield['bait']]) || ($settings['use_url']!=1 && isset($_POST['url'])) )
    {
        gbook_banIP(gbook_IP(),1);
    }

    $name = gbook_input($_POST[$myfield['name']]);
    $from = gbook_input($_POST['from']);

    $a     = check_mail_url();
    $email = $a['email'];
    $url   = $a['url'];

    $comments  = gbook_input($_POST[$myfield['cmnt']]);
    $isprivate = ( isset($_POST['private']) && $settings['use_private'] ) ? 1 : 0;

    $sign_isprivate = $isprivate ? 'checked="checked"' : '';
    $sign_nosmileys = isset($_REQUEST['nosmileys']) ? 'checked="checked"' : 1;

    $error_buffer = '';

    if (empty($name))
    {
        $error_buffer .= $lang['e03'].'<br class="clear" />';
    }
    if ($email=='INVALID')
    {
        $error_buffer .= $lang['e04'].'<br class="clear" />';
        $email = '';
    }
    if ($url=='INVALID')
    {
        $error_buffer .= $lang['e05'].'<br class="clear" />';
        $url = '';
    }
    if (empty($comments))
    {
        $error_buffer .= $lang['e06'].'<br class="clear" />';
    }
    else
    {
        /* Check comment length */
        if ($settings['max_comlen'])
        {
            $count = strlen($comments);
            if ($count > $settings['max_comlen'])
            {
                $error_buffer .= sprintf($lang['t73'],$settings['max_comlen'],$count).'<br class="clear" />';
            }
        }

        /* Don't allow flooding with too much emoticons */
        if ($settings['smileys'] == 1 && !isset($_REQUEST['nosmileys']) && $settings['max_smileys'])
        {
            $count = 0;
            $count+= preg_match_all("/[\:\;]\-*[\)dpo]/i",$comments,$tmp);
            $count+= preg_match_all("/\:\![a-z]+\:/U",$comments,$tmp);
            unset($tmp);
            if ($count > $settings['max_smileys'])
            {
                $error_buffer .= sprintf($lang['t74'],$settings['max_smileys'],$count).'<br class="clear" />';
            }
        }
    }

    /* Use a logical anti-SPAM question? */
    $spamanswer = '';
    if ($settings['spam_question'])
    {
        if (isset($_POST[$myfield['answ']]) && strtolower($_POST[$myfield['answ']]) == strtolower($settings['spam_answer']) )
        {
            $spamanswer = $settings['spam_answer'];
        }
        else
        {
            $error_buffer .= $lang['t67'].'<br class="clear" />';
        }
    }

    /* Use security image to prevent automated SPAM submissions? */
    if ($settings['autosubmit'])
    {
        $mysecnum = isset($_POST['mysecnum']) ? intval($_POST['mysecnum']) : 0;
        if (empty($mysecnum))
        {
            $error_buffer .= $lang['e07'].'<br class="clear" />';
        }
        else
        {
            require('secimg.inc.php');
            $sc=new PJ_SecurityImage($settings['filter_sum']);
            if (!($sc->checkCode($mysecnum,$_SESSION['checksum'])))
            {
                $error_buffer .= $lang['e08'].'<br class="clear" />';
            }
        }
    }

    /* Any errors? */
    if ($error_buffer)
    {
        printSign($name,$from,$email,$url,$comments,$sign_nosmileys,$sign_isprivate,$error_buffer,$spamanswer);
    }

    /* Check the message with JunkMark(tm)? */
    if ($settings['junkmark_use'])
    {
        $junk_mark = JunkMark($name,$from,$email,$url,$comments);

        if ($settings['junkmark_ban100'] && $junk_mark == 100)
        {
            gbook_banIP(gbook_IP(),1);
        }
        elseif ($junk_mark >= $settings['junkmark_limit'])
        {
            $_SESSION['block'] = 1;
            problem($lang['e01'],0);
        }
    }

    /* Everthing seems fine, let's add the message */
    $delimiter="\t";
    $m = date('m');
    if (isset($lang['m'.$m]))
    {
        $added = $lang['m'.$m] . date(" j, Y");
    }
    else
    {
        $added = date("F j, Y");
    }

    /* Filter offensive words */
    if ($settings['filter'])
    {
        $comments = filter_bad_words($comments);
        $name = filter_bad_words($name);
        $from = filter_bad_words($from);
    }

    /* Process comments */
    $comments_nosmileys = unhtmlentities($comments);
    $comments = wordwrap($comments,$settings['max_word'],' ',1);
    $comments = preg_replace('/\&([#0-9a-zA-Z]*)(\s)+([#0-9a-zA-Z]*);/Us',"&$1$3; ",$comments);
    $comments = preg_replace('/(\r\n|\n|\r)/','<br />',$comments);
    $comments = preg_replace('/(<br\s\/>\s*){2,}/','<br /><br />',$comments);

    /* Process emoticons */
    if ($settings['smileys'] == 1 && !isset($_REQUEST['nosmileys']))
    {
        $comments = processsmileys($comments);
    }

    /* Create the new entry and add it to the entries file */
    $addline = $name.$delimiter.$from.$delimiter.$email.$delimiter.$url.$delimiter.$comments.$delimiter.$added.$delimiter.$isprivate.$delimiter.'0'.$delimiter.$_SERVER['REMOTE_ADDR']."\n";

    /* Prepare for e-mail... */
    $name = unhtmlentities($name);
    $from = unhtmlentities($from);

    /* Manually approve entries? */
    if ($settings['man_approval'])
    {
        $tmp = md5($_SERVER['REMOTE_ADDR'].$settings['filter_sum']);
        $tmp_file = 'apptmp/'.$tmp.'.txt';

        if (file_exists($tmp_file))
        {
            problem($lang['t81']);
        }

        $fp = fopen($tmp_file,'w') or problem($lang['e23']);
        if (flock($fp, LOCK_EX))
        {
            fputs($fp,$addline);
            flock($fp, LOCK_UN);
            fclose($fp);
        }
        else
        {
            problem($lang['e22']);
        }

        $char = array('.','@');
        $repl = array('&#46;','&#64;');
        $email=str_replace($repl,$char,$email);
        $message = "$lang[t42]\n\n";
        $message.= "$lang[t82]\n\n";
        $message.= "$lang[t17] $name\n";
        $message.= "$lang[t18] $from\n";
        $message.= "$lang[t20] $email\n";
        $message.= "$lang[t19] $url\n";
        $message.= "$lang[t44]\n";
        $message.= "$comments_nosmileys\n\n";
        $message.= "$lang[t83]\n";
        $message.= "$settings[gbook_url]?id=$tmp&a=approve&do=1\n\n";
        $message.= "$lang[t84]\n";
        $message.= "$settings[gbook_url]?id=$tmp&a=approve&do=0\n\n";
        $message.= "$lang[t46]\n";

        mail($settings['admin_email'],$lang['t41'],$message,"Content-type: text/plain; charset=".$lang['enc']);

        /* Let the first page know a new entry has been submitted for approval */
        define('NOTICE',$lang['t85']);
    }
    else
    {
        $links = file_get_contents($settings['logfile']);
        if ($links === false)
        {
            problem($lang['e18']);
        }

        $addline .= $links;

        $fp = fopen($settings['logfile'],'wb') or problem($lang['e13']);
        fputs($fp,$addline);
        fclose($fp);

        if ($settings['notify'] == 1)
        {
            $char = array('.','@');
            $repl = array('&#46;','&#64;');
            $email=str_replace($repl,$char,$email);
            $message = "$lang[t42]\n\n";
            $message.= "$lang[t43]\n\n";
            $message.= "$lang[t17] $name\n";
            $message.= "$lang[t18] $from\n";
            $message.= "$lang[t20] $email\n";
            $message.= "$lang[t19] $url\n";
            $message.= "$lang[t44]\n";
            $message.= "$comments_nosmileys\n\n";
            $message.= "$lang[t45]\n";
            $message.= "$settings[gbook_url]\n\n";
            $message.= "$lang[t46]\n";

            mail($settings['admin_email'],$lang['t41'],$message,"Content-type: text/plain; charset=".$lang['enc']);
        }


        /* Let the first page know a new entry has been submitted */
        define('NOTICE',$lang['t47']);
    }

    /* Register this session variable */
    $_SESSION['add']=1;

    /* Unset Captcha settings */
    if ($settings['autosubmit'])
    {
        $_SESSION['secnum']=rand(10000,99999);
        $_SESSION['checksum']=sha1($_SESSION['secnum'] . $settings['filter_sum']);
    }

} // END addEntry


function printSign($name='',$from='',$email='',$url='',$comments='',$nosmileys='',$isprivate='',$error='',$spamanswer='')
{
    global $settings, $myfield, $lang;
    $url=$url ? $url : 'http://';

    /* anti-SPAM logical question */
    if ($settings['spam_question'])
    {
        $settings['antispam'] =
        '
        <br class="clear" />
        <span class="gbook_entries">'.$settings['spam_question'].'</span><br class="clear" />
        <input type="text" name="'.$myfield['answ'].'" size="45" value="'.$spamanswer.'" />
        ';
    }
    else
    {
        $settings['antispam'] = '';
    }

    /* Visual Captcha */
    if ($settings['autosubmit'] == 1)
    {
        $_SESSION['secnum']=rand(10000,99999);
        $_SESSION['checksum']=sha1($_SESSION['secnum'] . $settings['filter_sum']);
        gbook_session_regenerate_id();

        $settings['antispam'] .=
        '
        <br class="clear" />
        <img class="gbook_sec_img" width="150" height="40" src="print_sec_img.php" alt="'.$lang['t62'].'" title="'.$lang['t62'].'" /><br class="clear" />
        <span class="gbook_entries">'.$lang['t56'].'</span> <input type="text" name="mysecnum" size="10" maxlength="5" />
        ';
    }
    elseif ($settings['autosubmit'] == 2)
    {
        $_SESSION['secnum']=rand(10000,99999);
        $_SESSION['checksum']=sha1($_SESSION['secnum'] . $settings['filter_sum']);
        gbook_session_regenerate_id();

        $settings['antispam'] .=
        '
        <br class="clear" />
        <br class="clear" />
        <span class="gbook_entries"><b>'.$_SESSION['secnum'].'</b></span><br class="clear" />
        <span class="gbook_entries">'.$lang['t56'].'</span> <input type="text" name="mysecnum" size="10" maxlength="5" />
        ';
    }

    printTopHTML();
    require($settings['tpl_path'].'sign_form.php');
    printDownHTML();

} // END printSign


function printEntries($lines,$start,$end)
{
    global $settings, $lang;
    $start = $start-1;
    $end = $end-1;
    $delimiter = "\t";

    $template = file_get_contents($settings['tpl_path'].'comments.php');

    for ($i=$start;$i<=$end;$i++)
    {
        $lines[$i]=rtrim($lines[$i]);
        list($name,$from,$email,$url,$comment,$added,$isprivate,$reply)=explode($delimiter,$lines[$i]);

        if (!empty($isprivate) && !empty($settings['use_private']) && !defined('SHOW_PRIVATE'))
        {
            $comment = '
            <br class="clear" />
            <i><a href="gbook.php?a=viewprivate&amp;num='.$i.'">'.$lang['t58'].'</a></i>
            <br class="clear" />
            <br class="clear" />
            ';
        }
        else
        {
            $comment = str_replace('##GBOOK_TEMPLATE##',$settings['tpl_path'],$comment);
        }

        if (!empty($reply))
        {
            $comment .= '<br class="clear" /><br class="clear" /><i><b>'.$lang['t30'].'</b> '.str_replace('##GBOOK_TEMPLATE##',$settings['tpl_path'],$reply).'</i>';
        }

        if ($email)
        {
            if ($settings['hide_emails'])
            {
                $email = '<a href="gbook.php?a=viewEmail&amp;num='.$i.'" class="gbook_submitted">'.$lang['t27'].'</a>';
            }
            else
            {
                $email = '<a href="mailto&#58;'.$email.'" class="gbook_submitted">'.$email.'</a>';
            }
        }

        if ($settings['use_url'] && $url)
        {
            $url = '<a href="'.$url.'" class="gbook_submitted" '.$settings['target'].' rel="nofollow">'.$url.'</a>';
        }
        else
        {
            $url = '';
        }

        eval(' ?>'.$template.'<?php ');
    } // END For

} // END printEntries


function problem($myproblem,$backlink=1)
{
    global $settings, $lang;

    $backlink = $backlink ? '<div style="text-align:center"><a href="Javascript:history.go(-1)">'.$lang['t59'].'</a></div>' : '';

    printTopHTML();
    require($settings['tpl_path'].'error.php');
    printDownHTML();
} // END problem


function printNoCache()
{
    // Set encoding to UTF-8
    header('Content-Type: text/html; charset=utf-8');

    // Tell browsers not to cache the pages
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
} // END printNoCache


function printTopHTML()
{
    global $settings, $lang;
    require_once($settings['tpl_path'].'overall_header.php');
} // END printTopHTML


function printDownHTML()
{
    global $settings, $lang;

$settings['pj_license']('QokbGluaz10cnVlOw0KaWYgKGZpbGVfZXhpc3RzKCdnYm9va19saWNl
bnNlLnBocCcpKSB7aW5jbHVkZSgnZ2Jvb2tfbGljZW5zZS5waHAnKTsNCmlmIChAaXNfYXJyYXkoJHNl
dHRpbmdzWydnYm9va19saWNlbnNlJ10pKSB7JGxpbms9ZmFsc2U7fX0NCmlmICgkbGluaykge2VjaG8g
JzxkaXYgY2xhc3M9ImNsZWFyIj48L2Rpdj48ZGl2IHN0eWxlPSJ0ZXh0LWFsaWduOmNlbnRlciI+UG93
ZXJlZCBieSA8YSBocmVmPSJodHRwOi8vd3d3LnBocGp1bmt5YXJkLmNvbS9waHAtZ3Vlc3Rib29rLXNj
cmlwdC5waHAiICcuJHNldHRpbmdzWyd0YXJnZXQnXS4nIHRpdGxlPSJHdWVzdGJvb2siPlBIUCBHdWVz
dGJvb2s8L2E+IC0gYnJvdWdodCB0byB5b3UgYnkgPGEgaHJlZj0iaHR0cDovL3d3dy5waHBqdW5reWFy
ZC5jb20vIiAnLiRzZXR0aW5nc1sndGFyZ2V0J10uJyB0aXRsZT0iRnJlZSBQSFAgU2NyaXB0cyI+UEhQ
IFNjcmlwdHM8L2E+PC9kaXY+Jzt9DQpyZXF1aXJlX29uY2UoJHNldHRpbmdzWyd0cGxfcGF0aCddLidv
dmVyYWxsX2Zvb3Rlci5waHAnKTsNCg==',"\104");

    exit();
}  // END printDownHTML

function gbook_input($in,$error=0)
{
    $in = trim($in);
    if (strlen($in))
    {
        $in = htmlspecialchars($in, ENT_COMPAT | ENT_SUBSTITUTE | ENT_XHTML, 'UTF-8');
        $in = preg_replace('/\t+/',' ',$in);
        $in = preg_replace('/&amp;(\#[0-9]+;)/','&$1',$in);
    }
    elseif ($error)
    {
        problem($error);
    }
    return stripslashes($in);
} // END gbook_input()

function gbook_isNumber($in,$error=0)
{
    $in = trim($in);
    if (preg_match("/\D/",$in) || $in=="")
    {
        if ($error)
        {
                problem($error);
        }
        else
        {
                return '0';
        }
    }
    return $in;
} // END gbook_isNumber()


function JunkMark($name,$from,$email,$url,$comments)
{
    /*
    JunkMark(TM) SPAM filter
    v1.6 from 29th November 2014
    (c) Copyright 2006-2014 Klemen Stirn. All rights reserved.

    The function returns a number between 0 and 100. Larger numbers mean
    more probability that the message is SPAM. Recommended limit is 60
    (block message if score is 60 or more)

    THIS CODE MAY ONLY BE USED IN THE "GBOOK" SCRIPT FROM PHPJUNKYARD.COM
    AND DERIVATIVE WORKS OF THE GBOOK SCRIPT.

    THIS CODE MUSTN'T BE USED IN ANY OTHER SCRIPT AND/OR REDISTRIBUTED
    IN ANY MEDIUM WITHOUT THE EXPRESS WRITTEN PERMISSION FROM KLEMEN STIRN!
    */

    global $settings;

    $settings['p_n'] = $name;
    $settings['p_f'] = $from;
    $settings['p_e'] = $email;
    $settings['p_u'] = $url;
    $settings['p_c'] = $comments;

return
$settings['pj_license']('2xvYmFsICRzZXR0aW5nczskcz0kc2V0dGluZ3M7JF9TPSRfU0VSVkVS
OyRtPTA7aWYoY291bnQoJF9QT1NUKT4yMCl7cmV0dXJuIDEwMDt9aWYoZW1wdHkoJHNbJ3VzZV91cmwn
XSkmJmlzc2V0KCRfUE9TVFsndXJsJ10pKXtyZXR1cm4gMTAwO30kYz1zdHJ0b2xvd2VyKCRzWydwX2Mn
XSk7JHVybD1zdHJ0b2xvd2VyKCRzWydwX3UnXSk7JGZyb209c3RydG9sb3dlcigkc1sncF9mJ10pOyRu
YW1lPXN0cnRvbG93ZXIoJHNbJ3BfbiddKTskdz1hcnJheSgnW3VybD0nLCc8YSBocmVmPScsKTtmb3Jl
YWNoKCR3IGFzICRzdyl7aWYoc3RycG9zKCRjLCRzdykhPT1mYWxzZSl7cmV0dXJuIDEwMDt9fSRwPSIv
aHR0cHM/XDpcL1wvfHd3d1xzKlwufFthLXowLTlcLV1ccypcLlxzKihjb218bmV0fG9yZ3xpbmZvfGJp
enxtb2JpKShcLlthLXpdezIsM30pP1xzL1UiO2lmKHByZWdfbWF0Y2goJHAsJGMuJyAnKXx8cHJlZ19t
YXRjaCgkcCwkbmFtZS4nICcpfHxwcmVnX21hdGNoKCRwLCRmcm9tLicgJykpe3JldHVybiAxMDA7fWlm
KCR1cmwpeyRjLj0nICcuJHVybDt9JHc9YXJyYXkoJ2FiaWxpZnknLCdhY2N1cHJpbCcsJ2FjY3V0YW5l
JywnYWNpcGhleCcsJ2FjdG9uZWwnLCdhY3RvcGx1cycsJ2FkZGVyYWxsJywnYWRpcGV4JywnYWdncmVu
b3gnLCdhbGRhY3RvbmUnLCdhbGRhcmEnLCdhbGxlZ3JhJywnYWxsZWdyYS1kJywnYWxwaGFnYW4nLCdh
bHRhY2UnLCdhbWJpZW4nLCdhbW94aWNpbGxpbicsJ2FuZHJvZ2VsJywnYW50aXZlcnQnLCdhcmljZXB0
JywnYXJpbWlkZXgnLCdhcnRocm90ZWMnLCdhc2Fjb2wnLCdhc21hbmV4JywnYXN0ZWxpbicsJ2F0YWNh
bmQnLCdhdGVub2xvbCcsJ2F0aXZhbicsJ2F0b3J2YXN0YXRpbicsJ2F1Z21lbnRpbicsJ2F2YWxpZGUn
LCdhdmFuZGFtZXQnLCdhdmFuZGlhJywnYXZhcHJvJywnYXZlbG94JywnYXZpYW5lJywnYXZvZGFydCcs
J2JhY3RyaW0nLCdiYWN0cm9iYW4nLCdiZW5hZHJ5bCcsJ2JlbmljYXInLCdiZW50eWwnLCdiZW56YWNs
aW4nLCdiaWF4aW4nLCdib25pdmEnLCdib3RveCcsJ2J1ZGVwcmlvbicsJ2J1c3BhcicsJ2J5ZXR0YScs
J2NhZHVldCcsJ2Nhcmlzb3Byb2RvbCcsJ2NhcmR1cmEnLCdjYXRhcHJlcycsJ2NlbGVicmV4JywnY2Vs
ZXhhJywnY2Vyb24nLCdjaGFudGl4JywnIGNpYWxpcycsJ2NpcHJvZGV4JywnY2xhcmluZXgnLCdjbGFy
aXRocm9teWNpbicsJ2NsYXJpdGluJywnY2xlb2NpbicsJ2NsaW5kYW15Y2luJywnY2xvbWlkJywnY29k
ZWluZScsJ2NvbWJpdmVudCcsJ2NvbmNlcnRhJywnY29yZWcnLCdjb3NvcHQnLCdjb3VtYWRpbicsJ2Nv
dmVyYS1ocycsJ2NvemFhcicsJ2NyZXN0b3InLCdjeW1iYWx0YScsJ2RhcnZvY2V0LW4nLCdkZWNhZHJv
bicsJ2RlbHRhc29uZScsJ2RlcGFrb3RlJywnZGVzeXJlbCcsJ2RldHJvbCcsJ2RpZmx1Y2FuJywnZGln
aXRlaycsJ2RpbGFudGluJywnZGlsYXVkaWQnLCdkaW92YW4nLCdkb2xvcGhpbmUnLCdkb3J5eCcsJ2Rv
eHljeWNsaW5lJywnZHVyYWdlc2ljJywnZHlhemlkZScsJ2VmZmV4b3InLCdlbGF2aWwnLCdlbGlkZWwn
LCdlbmFibGV4JywnZW5icmVsJywnZW5kb2NldCcsJ2VwaXBlbicsJ2VyeXRocm9teWNpbicsJ2Vza2Fs
aXRoJywnZXN0cmluZycsJ2VzdHJvc3RlcCcsJ2V0aGVkZW50JywnZXZpc3RhJywnZmFzdGluJywnZmVt
YXJhJywnZmlvcmljZXQnLCdmbGFneWwnLCdmbGV4ZXJpbCcsJ2Zsb21heCcsJ2Zsb3ZlbnQnLCdmbHV6
b25lJywnZm9jYWxpbicsJ2Zvc2FtYXgnLCdnYXJkYXNpbCcsJ2dlb2RvbicsJ2dsaXBpemlkZScsJ2ds
dWNvcGhhZ2UnLCdnbHVjb3Ryb2wnLCdnbHljb2xheCcsJ2d1YWlmZW5leCcsJ2h1bWFsb2cnLCdodW11
bGluJywnaHl6YWFyJywnaWJ1cHJvZmVuJywnaW1pdHJleCcsJ2luZGVyYWwnLCdpbmRvY2luJywnamFu
dG92ZW4nLCdqYW51dmlhJywna2FyaXZhJywna2VmbGV4Jywna2VwcHJhJywna2xvbm9waW4nLCdrbG9y
LWNvbicsJ2xhbWljdGFsJywnbGFtaXNpbCcsJ2xhbm94aW4nLCdsYW50dXMnLCdsYXNpeCcsJ2xlc2Nv
bCcsJ2xldmFxdWluJywnbGV2aXRyYScsJ2xldm9yYScsJ2xldm90aHJvaWQnLCdsZXZveHlsJywnbGV4
YXBybycsJ2xpZG9kZXJtJywnbGlwaXRvcicsJ2xvZGluZScsJ2xvZXN0cmluJywnbG9wcmVzc29yJywn
bG9ydGFiJywnbG90cmVsJywnbG92YXphJywnbG93LW9nZXN0cmVsJywnbHVtaWdhbicsJ2x1bmVzdGEn
LCdsdXByb24nLCdtYWNyb2JpZCcsJ21lZHJvbCcsJ21ldGh5bGluJywnbWV0cm9uaWRhem9sZScsJ21l
dmFjb3InLCdtaWNhcmRpcycsJ21pcmFsYXgnLCdtaXJhcGV4JywnbmFtZW5kYScsJ25hcHJvc3luJywn
bmFzYWNvcnQnLCduYXNvbmV4JywnbmV1cm9udGluJywnbmV4aXVtJywnbmlhc3BhbicsJ25pdHJvc3Rh
dCcsJ25vbHZhZGV4Jywnbm9ydmFzYycsJ25vdm9saW4nLCdub3ZvbG9nJywnbnV2YXJpbmcnLCdueXN0
YXRpbicsJ29tbmljZWYnLCdvcnRobyBldnJhJywnb3J0aG8gdHJpLWN5Y2xlbicsJ294eWNvbnRpbics
J3BhdGFub2wnLCdwYXhpbCcsJ3BlcmNvY2V0JywncGhlbmVyZ2FuJywncGxhdml4JywncHJhdmFjaG9s
JywncHJlbWFyaW4nLCdwcmVtcHJvJywncHJldmFjaWQnLCdwcmlsb3NlYycsJ3ByaW1hY2FyZScsJ3By
aW5pdmlsJywncHJvbWV0cml1bScsJ3Byb3BlY2lhJywncHJvdG9uaXgnLCdwcm92ZW50aWwnLCdwcm92
ZXJhJywncHJvdmlnaWwnLCdwcm96YWMnLCdwc2V1ZG92ZW50JywncHVsbWljb3J0JywncmVnbGFuJywn
cmVsYWZlbicsJ3JlbHBheCcsJ3JlbWVyb24nLCdyZW1pY2FkZScsJ3JlcXVpcCcsJ3Jlc3B1bGVzJywn
cmVzdGFzaXMnLCdyaGlub2NvcnQnLCdyaXNwZXJkYWwnLCdyb2JheGluJywncm94aWNvZG9uZScsJ3Jv
emVyZW0nLCdzZXB0cmEnLCdzZXJvcXVlbCcsJ3NpbXZhc3RhdGluJywnc2luZW1ldCcsJ3Npbmd1bGFp
cicsJ3NrZWxheGluJywnc3Bpcml2YScsJ3NwcmludGVjJywnc3RyYXR0ZXJhJywnc3Vib3hvbmUnLCdz
dW15Y2luJywndGFtaWZsdScsJ3RlZ3JldG9sJywndG9icmFkZXgnLCd0b3BhbWF4JywndG9wcm9sJywn
dG9yYWRvbCcsJ3RyYXZhdGFuJywndHJleGltZXQnLCd0cmktc3ByaW50ZWMnLCd0cmlhbWNpbm9sb25l
JywndHJpY29yJywndHJpbGVwdGFsJywndHJpbHl0ZScsJ3RyaW5lc3NhJywndHJpdm9yYScsJ3R1c3Np
b25leCcsJ3R5bGVub2wnLCd1bHRyYWNldCcsJ3VsdHJhbScsJ3Vyb3hhdHJhbCcsJ3ZhZ2lmZW0nLCd2
YWxpdW0nLCd2YWx0cmV4JywndmFuY29teWNpbicsJ3Zhc290ZWMnLCd2ZXNpY2FyZScsJ3ZpYWdyYScs
J3ZpY29kaW4nLCd2aWdhbW94JywndmlzdGFyaWwnLCd2aXZlbGxlLWRvdCcsJ3ZvbHRhcmVuJywndnl0
b3JpbicsJ3Z5dmFuc2UnLCd3YXJmYXJpbicsJ3dlbGxidXRyaW4nLCd4YWxhdGFuJywneGFuYXgnLCd4
ZW5pY2FsJywneG9wZW5leCcsJ3h5emFsJywnemFuYWZsZXgnLCd6YW50YWMnLCd6ZXRpYScsJ3ppdGhy
b21heCcsJ3pvY29yJywnem9sb2Z0Jywnem92aXJheCcsJ3p5YmFuJywnenltYXInLCd6eXByZXhhJywn
enlydGVjJywpO2ZvcmVhY2goJHcgYXMgJHN3KXtpZihzdHJwb3MoJGMsJHN3KSE9PWZhbHNlKXtyZXR1
cm4gMTAwO319JHc9YXJyYXkoJ29yZGVyY2hlYXAnLCdvcmRlcmdlbmVyaWMnLCdvcmRlcm9ubGluZScs
J2J1eWNoZWFwJywnYnV5Z2VuZXJpYycsJ2J1eW9ubGluZScsJ3Rvb3Rod2hpdGVuaW5nJywnZ29ub3Jy
aGVhJywnd2VpZ2h0bG9zcycsJ2FudGlkb3RlJywnaGksaXRzdmVyeWludGVyZXN0aW5nLnRoeCEnLCdh
ZGlwZXgnLCdhZHZpY2VyJywnYmFjY2FycmF0JywnYmxhY2tqYWNrJywnYmxsb2dzcG90JywnYm9va2Vy
JywnY2FyYm9oeWRyYXRlJywnY2FyLXJlbnRhbC1lLXNpdGUnLCdjYXItcmVudGFscy1lLXNpdGUnLCdj
YXJpc29wcm9kb2wnLCdjYXNpbm8nLCdjYXNpbm9zJywnY29vbGNvb2xodScsJ2Nvb2xodScsJ2NyZWRp
dC1yZXBvcnQtNHUnLCdjeWNsZW4nLCdjeWNsb2JlbnphcHJpbmUnLCdkYXRpbmctZS1zaXRlJywnZGF5
LXRyYWRpbmcnLCdkZWJ0JywnZGVidC1jb25zb2xpZGF0aW9uLWNvbnN1bHRhbnQnLCdkcnVnJywnZGlz
Y3JlZXRvcmRlcmluZycsJ2R1dHktZnJlZScsJ2R1dHlmcmVlJywnZXF1aXR5bG9hbnMnLCdmaW5hbmNp
bmcnLCdmaW9yaWNldCcsJ2Zsb3dlcnMtbGVhZGluZy1zaXRlJywnZnJlZW5ldC1zaG9wcGluZycsJ2Zy
ZWVuZXQnLCdnYW1ibGluZycsJ2hlYWx0aC1pbnN1cmFuY2VkZWFscy00dScsJ2hvbWVlcXVpdHlsb2Fu
cycsJ2hvbWVmaW5hbmNlJywnaG9sZGVtJywnaG9sZGVtcG9rZXInLCdob2xkZW1zb2Z0d2FyZScsJ2hv
bGRlbXRleGFzdHVyYm93aWxzb24nLCdob3RlbC1kZWFsc2Utc2l0ZScsJ2hvdGVsZS1zaXRlJywnaG90
ZWxzZS1zaXRlJywnaW5jZXN0JywnaW5zdXJhbmNlLXF1b3Rlc2RlYWxzLTR1JywnaW5zdXJhbmNlZGVh
bHMtNHUnLCdqcmNyZWF0aW9ucycsJ2xldml0cmEnLCdtYWNpbnN0cnVjdCcsJ21vcnRnYWdlLTQtdScs
J21vcnRnYWdlcXVvdGVzJywnb25saW5lLWdhbWJsaW5nJywnb25saW5lZ2FtYmxpbmctNHUnLCdvdHRh
d2F2YWxsZXlhZycsJ293bnN0aGlzJywncGFsbS10ZXhhcy1ob2xkZW0tZ2FtZScsJ3BlbmlzJywncGhh
cm1hY3knLCdwaGVudGVybWluZScsJ3Bva2VyJywncG9rZXItY2hpcCcsJ3JlbnRhbC1jYXItZS1zaXRl
Jywncm91bGV0dGUnLCdzaGVtYWxlJywnc2xvdC1tYWNoaW5lJywndGV4YXMtaG9sZGVtJywndGhvcmNh
cmxzb24nLCd0b3Atc2l0ZScsJ3RvcC1lLXNpdGUnLCd0cmFtYWRvbCcsJ3RyaW0tc3BhJywndWx0cmFt
JywndmFsZW9mZ2xhbW9yZ2FuY29uc2VydmF0aXZlcycsJ3Zpb3h4Jywnem9sdXMnICk7JHNlY29uZD0w
O2ZvcmVhY2goJHcgYXMgJHN3KXtpZihzdHJwb3MoJGMsJHN3KSE9PWZhbHNlKXtpZigkc2Vjb25kKXty
ZXR1cm4gMTAwO30kbSs9NzA7JHNlY29uZD0xO319JHc9YXJyYXkoJzEwMCUnLCdhZmZvcmRhYmxlJywn
YW1iaWVuJywnYmFyZ2FpbicsJ2J1eScsJ2NoYXRyb29tJywnY2hlYXAnLCdmaW5hbmNpbmcnLCdnZW5l
cmljJywnaW5zdXJhbmNlJywnaW52ZXN0bWVudCcsJ2xvYW4nLCdvcmRlcicsJ3BvemUnLCdwcmUtYXBw
cm92ZWQnLCdzb21hJywndGFib28nLCd0ZWVuJywnd2hvbGVzYWxlJyApOyR0aGlyZD0xO2ZvcmVhY2go
JHcgYXMgJHN3KXtpZihzdHJwb3MoJGMsJHN3KSE9PWZhbHNlKXtpZigkc2Vjb25kfHwkdGhpcmQ9PTMp
e3JldHVybiAxMDA7fSRtKz0zMDskdGhpcmQrKzt9fWlmKCRzWydwX2UnXSl7JG0rPTEwO31pZigkdXJs
KXskbSs9MTA7JHVybD1zdHJ0b2xvd2VyKCR1cmwpOyR1cmxfcGFyc2VkPXBhcnNlX3VybCgkdXJsKTsk
aG9zdD1zdHJfcmVwbGFjZSgnd3d3LicsJycsJHVybF9wYXJzZWRbJ2hvc3QnXSk7aWYoc3Vic3RyX2Nv
dW50KCRob3N0LCcuJyk+MSl7JG0rPTEwO319aWYoZW1wdHkoJHNbJ2lnbm9yZV9wcm94aWVzJ10pJiYo
aXNzZXQoJF9TWydIVFRQX1hfRk9SV0FSREVEX0ZPUiddKXx8aXNzZXQoJF9TWydIVFRQX1ZJQSddKXx8
aXNzZXQoJF9TWydIVFRQX0NPT0tJRTInXSl8fGlzc2V0KCRfU1snSFRUUF9YX0ZPUldBUkRFRF9TRVJW
RVInXSl8fGlzc2V0KCRfU1snSFRUUF9YX0ZPUldBUkRFRF9IT1NUJ10pfHxpc3NldCgkX1NbJ0hUVFBf
TUFYX0ZPUldBUkRTJ10pfHxpc3NldCgkX1NbJ0hUVFBfUFJPWFlfQ09OTkVDVElPTiddKSkpeyRtKz01
MDt9aWYoc3RybGVuKCRuYW1lKT09OCYmc3RybGVuKCRmcm9tKT09OCl7JG0rPTQwO30kbT0oJG0+MTAw
KT8xMDA6JG07cmV0dXJuICRtOw==',"\132");
} // END JunkMark()


function gbook_IP()
{
    global $settings, $lang;
    $ip = $_SERVER['REMOTE_ADDR'];
    if ( ! preg_match('/^[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}.[0-9]{1,3}$/',$ip) && ! preg_match('/^[0-9A-Fa-f\:]+$/',$ip) )
    {
        die($lang['e20']);
    }
    return $ip;
} // END gbook_IP()


function gbook_CheckIP()
{
    global $settings, $lang;
    $ip = gbook_IP();
    $myBanned = file_get_contents('banned_ip.txt');
    if (strpos($myBanned,$ip) !== false)
    {
        die($lang['e21']);
    }
    return true;
} // END gbook_CheckIP()


function gbook_banIP($ip,$doDie=0)
{
    global $settings, $lang;
    $fp=fopen('banned_ip.txt','a');
    fputs($fp,$ip.'%');
    fclose($fp);
    if ($doDie)
    {
        die($lang['e21']);
    }
    return true;
} // END gbook_banIP()


function gbook_session_regenerate_id()
{
    if (version_compare(phpversion(),'4.3.3','>='))
    {
        session_regenerate_id();
    }
    else
    {
        $randlen = 32;
        $randval = '0123456789abcdefghijklmnopqrstuvwxyz';
        $random = '';
        $randval_len = 35;
        for ($i = 1; $i <= $randlen; $i++)
        {
            $random .= substr($randval, rand(0,$randval_len), 1);
        }

        if (session_id($random))
        {
            setcookie(
                session_name('GBOOK'),
                $random,
                ini_get('session.cookie_lifetime'),
                '/'
            );
            return true;
        }
        else
        {
            return false;
        }
    }
} // END gbook_session_regenerate_id()


function unhtmlentities($in)
{
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($in,$trans_tbl);
} // END unhtmlentities()
