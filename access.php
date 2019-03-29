<?php
/**
 * Created by PhpStorm.
 * User: simmo796
 * Date: 3/9/2019
 * Time: 10:56 PM
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$acl = new access(new \phpbb\user,new \phpbb\auth\auth,$phpbb_log);
exit($acl->getUser()->ip);
if (!defined('IN_PHPBB'))
{
    exit;
}
/*
//include_once '/var/sentora/hostdata/zadmin/public_html/autohotkey_com/boards/app.php';
/* @var $phpbb_root_path '/var/sentora/hostdata/zadmin/public_html/autohotkey_com/boards' */
/*if (empty($phpbb_root_path)) {$phpbb_root_path = '/var/sentora/hostdata/zadmin/public_html/autohotkey_com/boards';}
include_once $phpbb_root_path . '/../secDev/banip.php';
include_once $phpbb_root_path . '/../secDev/cfBan.php';
$user_posts_threshold = (int) 20;
*/
class access
{
    /** @var \phpbb\user $user*/
    private $user;
    /** @var \phpbb\auth\auth $auth*/
    private $auth;
    /** @var  $phpbb_log \phpbb\log\log_interface*/
    private $phpbb_log;
    /** @var string  */
    private $phpbb_root_path;
    /** @var  int */
    private $user_posts_threshold;
    /** @var array  */
    private
        $URLWhiteList;
    /**
     * access constructor.
     * @param \phpbb\user $user
     * @param \phpbb\auth\auth $auth
     * @param \phpbb\log\log_interface $phpbb_log
     * @param $phpbb_root_path
     */
    public function __construct(\phpbb\user $user, \phpbb\auth\auth $auth, \phpbb\log\log_interface $phpbb_log, $phpbb_root_path = './', $user_posts_threshold = 20 )
    {
        $user->session_begin();
        $this->user = $user;
        $this->auth = $auth;
        $this->phpbb_log = $phpbb_log;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->user_posts_threshold = $user_posts_threshold;
        $this->URLWhiteList = [
            'www.autohotkey.com',
            'github.com',
        ];
    }

    /**
     * @return array
     */
    public function getURLWhiteList()
    {
        return $this->URLWhiteList;
    }

    /**
     * @param array $URLWhiteList
     */
    public function setURLWhiteList($URLWhiteList)
    {
        $this->URLWhiteList = $URLWhiteList;
    }

    /**
     * @return int
     */
    public function getUserPostsThreshold()
    {
        return $this->user_posts_threshold;
    }

    /**
     * @param int $user_posts_threshold
     */
    public function setUserPostsThreshold($user_posts_threshold)
    {
        $this->user_posts_threshold = $user_posts_threshold;
    }

    /**
     * @return \phpbb\user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \phpbb\user $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return \phpbb\auth\auth
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * @param \phpbb\auth\auth $auth
     */
    public function setAuth($auth)
    {
        $this->auth = $auth;
    }

    /**
     * @return \phpbb\log\log_interface
     */
    public function getPhpbbLog()
    {
        return $this->phpbb_log;
    }

    /**
     * @param \phpbb\log\log_interface $phpbb_log
     */
    public function setPhpbbLog($phpbb_log)
    {
        $this->phpbb_log = $phpbb_log;
    }

    /**
     * @return mixed
     */
    public function getPhpbbRootPath()
    {
        return $this->phpbb_root_path;
    }

    /**
     * @param mixed $phpbb_root_path
     */
    public function setPhpbbRootPath($phpbb_root_path)
    {
        $this->phpbb_root_path = $phpbb_root_path;
    }

    public function filterMessage(\parse_message $message_parser){
        $regex4Url = '/([a-z]{1,2}tps?):\/\/((?:(?!(?:\/|#|\?|&)).)+)(\S*)/';

        $message = (string) $message_parser->message;
        $user = $this->getUser();
        $user_posts_threshold = $this->getUserPostsThreshold();


        # if this is a script run by the server ignore all these rules
        if ($user->ip !== '127.0.0.1') {
            # apply only to new users or guests
            if (!$user->data['is_registered'] ||
                $user->data['user_posts'] < $user_posts_threshold
            ) {

                ## find all url in the post

                if (preg_match_all($regex4Url, $message, $UrlList) > 0 ){
                    foreach($UrlList[2] as $key=>$URL){ ## cycle thru each URL and decide what to do about it
                        if (!in_array($URL,$URLWhiteList)) {
                            ## break the URL from being parsed by bbcode or clickable
                            $brokenLink = preg_replace( $regex4Url, '$1 $2 $3', $UrlList[0][$key]) . "  Broken Link for safety";
                            $message = str_replace( $UrlList[0][$key], $brokenLink, $message);
                        }
                    }
                    # add a log entry
                    $thisUser = (string) ($user->data['is_registered'] && $user->data['user_id'] != ANONYMOUS) ? $user->data['username'] : 'Guest';
                    $log_subject = empty($post_data['topic_title']) ? $post_data['post_subject'] : $post_data['topic_title'];
                    /*
                    $phpbb_log = $this->getPhpbbLog();
                    $phpbb_log->add('mod', $user->data['user_id'], $user->ip, 'LOG_POST_EDITED', false, array(
                        'forum_id' => $forum_id,
                        'topic_id' => $topic_id,
                        'post_id'  => $post_id,
                        $log_subject,
                        $thisUser,
                        'Post had links edited'
                    ));*/
                }
            }
        }

        return $message;


    }



}