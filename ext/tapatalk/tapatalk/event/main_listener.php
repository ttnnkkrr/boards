<?php
/**
*
* @package phpBB Extension - Acme Demo
* @copyright (c) 2013 phpBB Group
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace tapatalk\tapatalk\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

defined('MBQ_IN_IT') or define('MBQ_IN_IT', true);

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
    static public function getSubscribedEvents()
    {
        return array(
            'core.user_setup'                       => 'tapa_setup',
            'core.page_header_after'                => 'tapa_header',
            'core.submit_post_end'                  => 'tapa_submit_post_end',
            'core.submit_pm_after'                  => 'tapa_submit_pm_after',
            'core.functions.redirect'               => 'tapa_redirect',
            'core.viewonline_overwrite_location'    => 'tapa_viewonline',
            'core.viewtopic_before_f_read_check'    => 'tapa_viewtopic',
            'core.viewtopic_post_rowset_data'       => 'tapa_modify_post_row',
            'core.ucp_pm_view_messsage'             => 'tapa_view_message',
            'core.delete_posts_after'               => 'tapa_delete_posts_after',
            'core.delete_topics_after_query'        => 'tapa_delete_topics_after_query'
//old ones
            //'core.page_footer'                      => 'tapa_page_footer',
            //'core.viewforum_modify_topics_data'     => 'tapa_topics',
            //'core.viewtopic_modify_post_data'       => 'tapa_threads',
            //'core.login_forum_box'                  => 'tapa_login_forum',
            //'core.viewonline_overwrite_location'    => 'tapa_viewonline',
            //'core.search_modify_tpl_ary'            => 'tapa_search',
            //'core.submit_post_end'                  => 'tapa_new_post',
            //'core.viewonline_modify_sql'            => 'tapa_showonline',
            //'core.posting_modify_template_vars'     => 'tapa_post',
            //'core.memberlist_view_profile'          => 'tapa_userinfo',
        );
    }

    /* @var \phpbb\controller\helper */
    protected $helper;

    /* @var \phpbb\template\template */
    protected $template;

    /* @var \phpbb\user */
    protected $user;

    /* @var \phpbb\auth\auth */
    protected $auth;
    /**
    * Constructor
    *
    * @param \phpbb\controller\helper   $helper     Controller helper object
    * @param \phpbb\template            $template   Template object
    */
    public function __construct(\phpbb\template\template $template, \phpbb\user $user, \phpbb\auth\auth $auth)
    {
        $this->template = $template;
        $this->user = $user;
        $this->auth = $auth;
    }
    public function tapa_submit_post_end($event)
    {
        global $config,$phpbb_root_path;
        if(isset($config['tapatalk_push_key']) && $config['tapatalk_push_key'] != "")
        {
            if(!class_exists('TapatalkPush'))
            {
                $tapatalk_dir = isset($config['tapatalkdir']) && !empty($config['tapatalkdir']) ? $config['tapatalkdir'] : 'mobiquo';
                $tapatalk_dir_url = $phpbb_root_path . $tapatalk_dir;
                include_once($tapatalk_dir_url . '/push/TapatalkPush.php');
            }
            $TapatalkPush = new \TapatalkPush();
            $data = $event->get_data();

            if(isset($data['post_visibility']) && $data['post_visibility'] != 1)
            {
                return;
            }
            switch($data['mode'])
            {
                case 'post':
                    {
                        $TapatalkPush->doPushPost($data['data']);
                        $TapatalkPush->doPushTag($data['data']);
                        break;
                    }
                case 'reply':
                    {
                        $TapatalkPush->doPushReply($data['data']);
                        $TapatalkPush->doPushQuote($data['data']);
                        $TapatalkPush->doPushTag($data['data']);
                        break;
                    }
                case 'quote':
                    {
                        $TapatalkPush->doPushQuote($data['data']);
                        $TapatalkPush->doPushReply($data['data']);
                        $TapatalkPush->doPushTag($data['data']);
                        break;
                    }
            }
        }
    }

    public function tapa_submit_pm_after($event)
    {
        global $config,$phpbb_root_path;
        if(isset($config['tapatalk_push_key']) && $config['tapatalk_push_key'] != "")
        {
            if(!class_exists('TapatalkPush'))
            {
                $tapatalk_dir = isset($config['tapatalkdir']) && !empty($config['tapatalkdir']) ? $config['tapatalkdir'] : 'mobiquo';
                $tapatalk_dir_url = $phpbb_root_path . $tapatalk_dir;
                include_once($tapatalk_dir_url . '/push/TapatalkPush.php');
            }
            $TapatalkPush = new \TapatalkPush();
            $data = $event->get_data();

            $TapatalkPush->doPushPm($data['pm_data'], $data['pm_data']['recipients']);
        }
    }
    public function tapa_delete_posts_after($event)
    {
        global $config,$phpbb_root_path;
        $data =$event->get_data();
        if($data['where_type'] =='post_id') //only process post deletion if is only post and no whole topic
        {
            if(isset($config['tapatalk_push_key']) && $config['tapatalk_push_key'] != "")
            {
                if(!class_exists('TapatalkPush'))
                {
                    $tapatalk_dir = isset($config['tapatalkdir']) && !empty($config['tapatalkdir']) ? $config['tapatalkdir'] : 'mobiquo';
                    $tapatalk_dir_url = $phpbb_root_path . $tapatalk_dir;
                    include_once($tapatalk_dir_url . '/push/TapatalkPush.php');
                }
                $TapatalkPush = new \TapatalkPush();
                $data = $event->get_data();

                $TapatalkPush->doPushDelete($data);
            }
        }
    }

    public function tapa_delete_topics_after_query($event)
    {
        global $config,$phpbb_root_path;
        if(isset($config['tapatalk_push_key']) && $config['tapatalk_push_key'] != "")
        {
            if(!class_exists('TapatalkPush'))
            {
                $tapatalk_dir = isset($config['tapatalkdir']) && !empty($config['tapatalkdir']) ? $config['tapatalkdir'] : 'mobiquo';
                $tapatalk_dir_url = $phpbb_root_path . $tapatalk_dir;
                include_once($tapatalk_dir_url . '/push/TapatalkPush.php');
            }
            $TapatalkPush = new \TapatalkPush();
            $data = $event->get_data();

            $TapatalkPush->doPushDelete($data);
        }
    }
    public function tapa_header($event)
    {
        global $user, $config, $phpbb_root_path,$request,$template,$topic_data, $forum_id, $cache, $phpEx;

        $board_url = generate_board_url();
        $mobiquoHideForumId = $this->getTapatalkConfigValue('mobiquo_hide_forum_id');
        if(!empty($forum_id) && !empty($mobiquoHideForumId))
        {
            $hide_forum_ids = explode(',', $mobiquoHideForumId);
            if(in_array($forum_id, $hide_forum_ids))
            {
                return ;
            }
        }

        $request->enable_super_globals();

        $app_forum_name = $config['sitename'];
        $board_url .=  '/';
        $tapatalk_dir = isset($config['tapatalkdir']) && !empty($config['tapatalkdir']) ? $config['tapatalkdir'] : 'mobiquo';
        $tapatalk_dir_url = $board_url . $tapatalk_dir;
        $is_mobile_skin = 0;
        $api_key = $config['tapatalk_push_key'];
        $location = $user->extract_current_page($phpbb_root_path);
        $app_location = $this->get_tapatalk_location($location);

        $app_banner_enable =  $config['tapatalk_app_banner_enable'];
        $google_indexing_enabled = $config['tapatalk_google_indexing_enabled'];
        $facebook_indexing_enabled = $config['tapatalk_facebook_indexing_enabled'];
        $twitter_indexing_enabled = $config['tapatalk_twitter_indexing_enabled'];

        if(!defined('IN_MOBIQUO')) define('IN_MOBIQUO', true);
        if(!defined('TT_ROOT'))
        {
            if(empty($config['tapatalkdir'])) $config['tapatalkdir'] = 'mobiquo';
            define('TT_ROOT', $phpbb_root_path . $config['tapatalkdir'] . '/');
        }
        require_once TT_ROOT . 'mbqFrame/3rdLib/classTTConnection.' . $phpEx;
        require_once TT_ROOT . 'tapatalkFunctions.php';
        if($cache->get('_tapatalk_banner_control'))
        {
            $TT_bannerControlData = $cache->get('_tapatalk_banner_control');
        }
        else
        {
            $TT_bannerControlData  = $this->getTapatalkConfigValue('tapatalk_banner_control');
            $TT_bannerControlData = !empty($TT_bannerControlData) ? unserialize($TT_bannerControlData)  :  array('banner_enable' => -1);
            $cache->put('_tapatalk_banner_control', $TT_bannerControlData);
        }
        $TT_connection = new \classTTConnection();

        $TT_bannerControlData = $TT_connection->calcSwitchOptions($TT_bannerControlData, $app_banner_enable, $google_indexing_enabled, $facebook_indexing_enabled, $twitter_indexing_enabled);

        $app_forum_name = $config['sitename'];
        if(isset($TT_bannerControlData['byo_info']) && !empty($TT_bannerControlData['byo_info']))
        {
            $app_rebranding_id = $TT_bannerControlData['byo_info']['app_rebranding_id'];
            $app_url_scheme = $TT_bannerControlData['byo_info']['app_url_scheme'];
            $app_icon_url = $TT_bannerControlData['byo_info']['app_icon_url'];
            $app_name = $TT_bannerControlData['byo_info']['app_name'];
            $app_alert_status = $TT_bannerControlData['byo_info']['app_alert_status'];
            $app_alert_message = $TT_bannerControlData['byo_info']['app_alert_message'];

            $app_android_id = $TT_bannerControlData['byo_info']['app_android_id'];
            $app_android_description = $TT_bannerControlData['byo_info']['app_android_description'];
            $app_banner_message_android = $TT_bannerControlData['byo_info']['app_banner_message_android'];
            $app_banner_message_android = preg_replace('/\r\n/','<br>',$app_banner_message_android);

            $app_ios_id = $TT_bannerControlData['byo_info']['app_ios_id'];
            $app_ios_description = $TT_bannerControlData['byo_info']['app_ios_description'];
            $app_banner_message_ios = $TT_bannerControlData['byo_info']['app_banner_message_ios'];
            $app_banner_message_ios = preg_replace('/\r\n/','<br>',$app_banner_message_ios);
        }
        //twitter card
        $twc_site = isset($TT_bannerControlData['twitter_account']) && !empty($TT_bannerControlData['twitter_account']) ? $TT_bannerControlData['twitter_account'] : "tapatalk";
        $twc_title = '';
        $page_type = "";
        preg_match('/location=(\w+)/is', $app_location, $matches);
        if(!empty($matches[1]))
        {
            if($matches[1] == 'message')
            {
                $matches[1] = 'pm';
            }
            $page_type = $matches[1];
        }

        if(strpos($location['query_string'],'view=next') !== false || strpos($location['query_string'],'view=previous') !== false || strpos($location['query_string'],'view=print') !== false)
        {
            $page_type = "index";
        }
        if($page_type == "index")
        {
            $page_type = "home";
        }
        if($page_type =="topic")
        {
            $twc_title = $topic_data['topic_title'];
        }

        //liveramp
        $liveramp_trak_tag = !isset($TT_bannerControlData['track_tag']) ? 0 : (int)$TT_bannerControlData['track_tag'];
        $liveramp_tag_id = !isset($TT_bannerControlData['tag_id']) ? '' : $TT_bannerControlData['tag_id'];

        if (file_exists($phpbb_root_path .  $tapatalk_dir. '/smartbanner/head.inc.php')) {
            include_once($phpbb_root_path .  $tapatalk_dir . '/smartbanner/head.inc.php');
        }
        if (isset($app_head_include))
        {
            $this->template->append_var("META", $app_head_include);
        }
    }

    public function tapa_viewonline($event)
    {
        global $username_full, $config, $phpbb_root_path, $on_page, $row;
        $config['tapatalkdir'] = !empty($config['tapatalkdir']) ? $config['tapatalkdir'] : 'mobiquo';
        if(isset($on_page[1]) && ($on_page[1] == $config['tapatalkdir']))
        {
            $icon_url = $phpbb_root_path.$config['tapatalkdir'].'/images/tapatalk-online.png?new';
            $icon_byo_url = $phpbb_root_path.$config['tapatalkdir'].'/images/byo-online.png';
            if(strpos($row['session_browser'],"BYO"))
            {
                $username_full = $username_full.'&nbsp;<img src="'.$icon_byo_url.'" style="vertical-align: middle;cursor:pointer;">';
                $row['is_byo'] = true;
            }
            else
            {
                $row['is_tapatalk'] = true;
                $username_full = $username_full.'&nbsp;<img src="'.$icon_url.'" style="vertical-align: middle;cursor:pointer;">';
            }
        }
    }

    public function get_tapatalk_location($location)
    {
        global $user,$phpbb_root_path,$config;
        $param_arr = array();
        switch ($location['page_name'])
        {
            case "viewforum.php":
                if(!empty($_GET['f']))
                {
                    $param_arr['fid'] = $_GET['f'];
                    if(isset($_GET['start']))
                    {
                        $param_arr['page'] = intval($_GET['start']/$config['topics_per_page']) + 1;
                        $param_arr['perpage'] = intval($config['topics_per_page']);
                    }
                    else
                    {
                        $param_arr['page'] =  1;
                        $param_arr['perpage'] = intval($config['topics_per_page']);
                    }
                }
                $param_arr['location'] = 'forum';
                break;
            case "index.php":
            case '':
                $param_arr['location'] = 'index';
                break;
            case "ucp.php":
                if(!empty($_GET['i']) && ($_GET['i'] == "pm"))
                {
                    $param_arr['location'] = 'message';
                    if(!empty($_GET['p']))
                    $param_arr['mid'] = $_GET['p'];
                }
                if(!empty($_GET['mode']) && ($_GET['mode'] == 'login'))
                {
                    $param_arr['location'] = 'login';
                }
                break;
            case "search.php":
                $param_arr['location'] = "search";
                break;
            case "viewtopic.php":
                if(!empty($_GET['t']))
                {
                    $param_arr['location'] = 'topic';
                    if(isset($_GET['f']))
                    {
                        $param_arr['fid'] = $_GET['f'];
                    }
                    $param_arr['tid'] = $_GET['t'];
                    if(isset($_GET['start']))
                    {
                        $param_arr['page'] = intval($_GET['start']/$config['posts_per_page']) + 1;
                        $param_arr['perpage'] = intval($config['posts_per_page']);
                    }
                    else
                    {
                        $param_arr['page'] =  1;
                        $param_arr['perpage'] = intval($config['posts_per_page']);
                    }
                    if(isset($_GET['p']))
                    {
                        $param_arr['pid'] = $_GET['p'];
                        $param_arr['location'] = 'post';
                    }
                }
                break;
            case "memberlist.php":
                   if(!empty($_GET['mode']) && $_GET['mode'] == "viewprofile" && !empty($_GET['u']))
                {
                    $param_arr['location'] = 'profile';
                    $param_arr['uid'] = $_GET['u'];
                }

                break;
            case "viewonline.php":
                $param_arr['location'] = 'online';
                break;
            default:
                $param_arr['location'] = 'index';
                break;
        }
        $queryString = http_build_query($param_arr);
        $url = generate_board_url() . '/?' .$queryString;
        $url = preg_replace('/^(https|http):\/\//isU', '', $url);
        return $url;
    }

    public function tapa_viewtopic($event)
    {
        global $config,$phpbb_root_path;

        if (isset($_GET['watch']) && request_var('watch', '') == 'topic')
        {
            if(!class_exists('TapatalkPush'))
            {
                $tapatalk_dir = isset($config['tapatalkdir']) && !empty($config['tapatalkdir']) ? $config['tapatalkdir'] : 'mobiquo';
                $tapatalk_dir_url = $phpbb_root_path . $tapatalk_dir;
                include_once($tapatalk_dir_url . '/push/TapatalkPush.php');
            }
            $TapatalkPush = new \TapatalkPush();

            $data = $event->get_data();

            $TapatalkPush->doPushSubTopic($data['topic_data']);
        }
    }

    public function tapa_modify_post_row($event)
    {
        try
        {
            global $row, $config, $rowset_data;

            if(defined('IN_MOBIQUO'))
            {
                return ;
            }
            $message = $row['post_text'];
            $message = preg_replace('#<a [^>]*?href="https?://(www\.)?vimeo\.com/(\d+)"[^>]*?>[^>]*?</a>#si',
            '<iframe src="https://player.vimeo.com/video/$2" width="500" height="300" frameborder="0"></iframe>', $message);
            // display emoji from app
            $protocol = ($config['cookie_secure'])  ? 'https' : 'http';
            $message = preg_replace('/\[emoji(\d+)\]/i', '<img src="'.$protocol.'://emoji.tapatalk-cdn.com/emoji\1.png" />', $message);

            $data = $event->get_data();
            if(!empty($message))
            {
                $data['rowset_data']['post_text'] = $rowset_data['post_text'] = $message;
            }
            $event->set_data($data);
        }
        catch(Exception $ex)
        {}
    }

    public function tapa_view_message($event)
    {
        global $config;

        if(defined('IN_MOBIQUO'))
        {
            return ;
        }

        $data = $event->get_data();

        $message = $data['msg_data']['MESSAGE'];
        $message = preg_replace('#<a [^>]*?href="https?://(www\.)?vimeo\.com/(\d+)"[^>]*?>[^>]*?</a>#si',
        '<iframe src="https://player.vimeo.com/video/$2" width="500" height="300" frameborder="0"></iframe>', $message);
        // display emoji from app
        $protocol = ($config['cookie_secure'])  ? 'https' : 'http';
        $message = preg_replace('/\[emoji(\d+)\]/i', '<img src="'.$protocol.'://emoji.tapatalk-cdn.com/emoji\1.png" />', $message);
        if(!empty($message))
        {
            $data['msg_data']['MESSAGE'] = $message;
        }

        $event->set_data($data);
    }

    //public function tapa_login_forum($event)
    //{
    //    if(defined('IN_MOBIQUO'))
    //    {
    //        $result = array(
    //            'result'       => (boolean)0,
    //            'result_text'  => '',
    //        );
    //        mobi_resp($result);
    //        exit;
    //    }
    //}

    //public function tapa_page_footer($event)
    //{
    //    if(defined('IN_MOBIQUO'))
    //    {
    //        global $request_method;

    //        switch($request_method)
    //        {
    //            //here we deal with the ERRORS
    //            case 'login':
    //                if(!tt_get_user_by_name(request_var('username','')))
    //                {
    //                    $status = 2;
    //                    $response = array(
    //                        'result'          => false,
    //                        'result_text'     => preg_replace('/\%s/si','',$this->user->lang['LOGIN_ERROR_USERNAME']),
    //                        'status'          => (string)$status,
    //                    );
    //                }
    //                else
    //                {
    //                    $status = 0;
    //                    $response = array(
    //                        'result'          => false,
    //                        'result_text'     => preg_replace('/\%s/si','',$this->user->lang['LOGIN_ERROR_PASSWORD']),
    //                        'status'          => (string)$status,
    //                    );
    //                }
    //                print_r(mobi_xmlrpc_encode($response,true));
    //                break;
    //            //other final functions
    //            case 'reply_post':
    //                trigger_error($this->user->lang['FLOOD_ERROR']);
    //                break;
    //            case 'get_user_info':
    //                trigger_error($this->user->lang['LOGIN_REQUIRED']);
    //                break;
    //            case 'get_thread':
    //            case 'get_topic':
    //            case 'get_online_users':
    //            case 'login_forum':
    //                call_user_func($request_method.'_func');
    //                break;
    //            default://if default we output html to show what the page is (for_dev (need_remove (undo
    //                return;
    //        }
    //        exit;
        //}
    //}

    public function tapa_redirect($event)
    {
        if(defined('IN_MOBIQUO'))
        {
           $data =  $event->get_data();
           $data['return'] = true;
           $event->set_data($data);
        }
    }


    public function tapa_setup($event)
    {
        global $db, $config, $request_method, $request_params;
        global $perpage, $topic_subscribed;
        //if(defined('IN_MOBIQUO'))
        //{
        //    //For check_form_key() func.
        //    $evt = $event->get_data();
        //    $evt['user_data']['user_form_salt'] = 'TapatalkApp';
        //    $evt['user_data']['user_lastvisit'] = 0;
        //    $event->set_data($evt);

        //    switch($request_method)
        //    {
        //        case 'get_thread':
        //        case 'search':
        //        case 'get_topic':
        //            $config['topics_per_page'] = $config['posts_per_page'] = $perpage;
        //            break;
        //        case 'login':
        //            $config['max_login_attempts'] = '100';
        //            break;
        //        case 'reply_post':
        //            $topic_id = $request_params[1];
        //            if ($config['allow_topic_notify'] && $evt['user_data']['is_registered'])
        //            {
        //                $sql = 'SELECT topic_id
        //                    FROM ' . TOPICS_WATCH_TABLE . '
        //                    WHERE topic_id = ' . $topic_id . '
        //                        AND user_id = ' . $evt['user_data']['user_id'];
        //                $result = $db->sql_query($sql);
        //                $topic_subscribed = (int) $db->sql_fetchfield('topic_id');
        //                $db->sql_freeresult($result);
        //            }
        //    }
        //}
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = array(
            'ext_name' => 'tapatalk/tapatalk',
            'lang_set' => 'common',
        );
        $event['lang_set_ext'] = $lang_set_ext;
    }

    //public function tapa_topics($event)
    //{
    //    if(defined('IN_MOBIQUO'))
    //    {
    //        global $request_method;

    //        switch ($request_method)
    //        {
    //            case 'get_topic':
    //                call_user_func($request_method.'_sub_func', $event->get_data());
    //        }
    //        return;
    //    }
    //}

    //public function tapa_threads($event)
    //{
    //    if(defined('IN_MOBIQUO'))
    //    {
    //        global $request_method;

    //        switch ($request_method)
    //        {
    //            case 'get_thread':
    //                call_user_func($request_method.'_sub_func', $event->get_data());
    //        }
    //        return;
    //    }
    //}

    //public function tapa_showonline($event)
    //{
    //    if(defined('IN_MOBIQUO'))
    //    {
    //        global $request_method, $guest_count;

    //        switch($request_method)
    //        {
    //            case 'get_online_users':
    //                $evt = $event->get_data();
    //                $evt['show_guests'] = 0;
    //                $guest_count = $evt['guest_counter'];
    //        }
    //    }
    //}

    //public function tapa_viewonline($event)
    //{
    //    if(defined('IN_MOBIQUO'))
    //    {
    //        global $request_method;

    //        switch ($request_method)
    //        {
    //            case 'get_online_users':
    //                call_user_func($request_method.'_sub_func', $event->get_data());
    //        }
    //        return;
    //    }
    //}

    //public function tapa_search($event)
    //{
    //    if(defined('IN_MOBIQUO'))
    //    {
    //        global $request_method;

    //        switch ($request_method)
    //        {
    //            case 'search':
    //                call_user_func($request_method.'_sub_func', $event->get_data());
    //        }
    //        return;
    //    }

    //}

    //public function tapa_new_post($event)
    //{
    //    if(defined('IN_MOBIQUO'))
    //    {
    //        global $request_method;
    //        $evt = $event->get_data();

    //        switch($request_method)
    //        {
    //            case 'new_topic':
    //                call_user_func($request_method.'_func',$evt);
    //                break;
    //        }
    //    }
    //}

    //public function tapa_post($event)
    //{
    //    if(defined('IN_MOBIQUO'))
    //    {
    //        global $request_method;

    //        switch ($request_method)
    //        {
    //            case 'get_quote_post':
    //            case 'get_raw_post':
    //                call_user_func($request_method.'_func', $event->get_data());
    //        }
    //        return;
    //    }
    //}

    //public function tapa_userinfo($event)
    //{
    //    if(defined('IN_MOBIQUO'))
    //    {
    //        global $request_method;

    //        switch ($request_method)
    //        {
    //            case 'get_user_info':
    //                call_user_func($request_method.'_func', $event->get_data());
    //        }
    //        return;
    //    }
    //}

    function getTextConfigKeys()
    {
        return array('tapatalk_custom_replace','tapatalk_forum_read_only','mobiquo_hide_forum_id');
    }
    function getTapatalkConfigValue($key)
    {
        global $config, $phpbb_container;
        if(in_array($key, $this->getTextConfigKeys()))
        {
            $value = $config[$key];
            if(empty($value))
            {
                $value = $phpbb_container->get('config_text')->get($key);
            }
            else
            {
                $phpbb_container->get('config_text')->set($key, $value);
                set_config($key, '');
            }
        }
        else
        {
            $value = $config[$key];
        }
        return $value;
    }
    function setTapatalkConfigValue($key, $value)
    {
        global $config, $phpbb_container;
        if(in_array($key, $this->getTextConfigKeys()))
        {
            $phpbb_container->get('config_text')->set($key, $value);
            set_config($key, '');
        }
        else
        {
            set_config($key, $value);
        }
        return $value;
    }
}
