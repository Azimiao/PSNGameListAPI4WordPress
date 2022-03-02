<?php

/**
 * Plugin Name: PSNGameListApi
 * Plugin URI: https://www.azimiao.com
 * Description: 一个WP用的PSN游戏库 API，用来提供显示 PSN 游戏列表的接口
 * Version: 1.0.0b2
 * Author: 野兔#梓喵出没
 * Author URI: https://www.azimiao.com
 */
require_once("Functions/wp-network.php");


class Azimiao_PSN_Api
{
    private $plugin_version = "v1.0.0b2";

    /**==================Options=================== **/

    private $optionName = "azimiao_psn_api";


    //基础参数
    private $api_base = 'api_base';

    private $azimiao_invitecode = "azimiao_invitecode";

    private $psn_account_id = "psn_account_id";

    private $psn_account_npsso = "psn_account_npsso";

    private $psn_client_id = "psn_client_id";

    //中间参数
    private $psn_account_token = "psn_account_token";

    private $psn_account_token_expire = "psn_account_token_expire";





    function __construct()
    {
        //创建菜单
        add_action("admin_menu", array($this, "initAdminPage"));
        //注册短代码
        add_action('init', array($this, "register_shortcodes"));


        add_action("wp_ajax_nopriv_GetTrophyList", array($this,"GetTrophyList"));
        add_action("wp_ajax_GetTrophyList", array($this,"GetTrophyList"));

        $options = $this->getOption();
        
    }

    public function register_shortcodes()
    {
        add_shortcode('psn_list_demo', array($this, 'list_demo'));
    }


    public function list_demo(){
        
    }


    //获取存储的配置信息
    function getOption()
    {
        $options = get_option($this->optionName);
        if (!is_array($options)) {
            $options[$this->api_base] = "https://api.azimiao.com/psn";
                
            $options[$this->azimiao_invitecode] = "";

            $options[$this->psn_account_id] = 0;
            $options[$this->psn_account_npsso] = "";

            $options[$this->psn_account_token] = "";
            $options[$this->psn_account_token_expire] = -1;



            update_option($this->optionName, $options);
        }

        return $options;
    }

    private $nonceflag = "azimiao_psn_page";
    private $nonceflagName = "azimiao_psn_page_save";

    function initAdminPage()
    {
        $NonceFlagTest = false;
        $options = $this->getOption();

        add_options_page("PSN API设置", "PSN API设置", "manage_options", "azimiao_psn_page_setting", array($this, "optionPage"));


        //TODO:处理提交内容
        if (isset($_POST[$this->nonceflagName])) {
            $Nonce = $_POST[$this->nonceflagName];

            if (wp_verify_nonce($Nonce, $this->nonceflag)) {
                $NonceFlagTest = true;
            }
        }

        $checkFlag1 = is_admin() && isset($_POST['psn-page-save']);
        
        $checkFlag2 = is_admin() && isset($_POST['psn-page-clear']);
        if ($NonceFlagTest) {
            //check Successful,do save and exec
            if($checkFlag1){
                $options[$this->api_base] = strval($_POST[$this->api_base] ?? "https://api.azimiao.com/psn");
                    
                $options[$this->azimiao_invitecode] = strval($_POST[$this->azimiao_invitecode] ?? "");

                $options[$this->psn_account_id] = intval($_POST[$this->psn_account_id] ?? 0);
                $options[$this->psn_account_npsso] = strval($_POST[$this->psn_account_npsso] ?? "");


                update_option($this->optionName, $options);

                echo "<div id='message' class='updated fade'><p><strong>数据已更新</strong></p></div>";
            }else{
                $options[$this->psn_account_token] = "";
                $options[$this->psn_account_token_expire] = -1;
                update_option($this->optionName, $options);
                echo "<div id='message' class='updated fade'><p><strong>已清除Token信息</strong></p></div>";

            }
        } 
    }


    //输出后台页面
    function optionPage()
    {
        $options = $this->getOption();
?>
        <style type="text/css">
            #pure_form {
                font-family: "Century Gothic", "Segoe UI", Arial, "Microsoft YaHei", Sans-Serif;
            }

            .wrap {
                padding: 10px;
                font-size: 12px;
                line-height: 24px;
                color: #383838;
            }

            .otakutable td {
                vertical-align: top;
                text-align: left;
                border: none;
                font-size: 12px;
            }

            .top td {
                vertical-align: middle;
                text-align: left;
                border: none;
                font-size: 12px;
            }

            table {
                border: none;
                font-size: 12px;
            }

            pre {
                white-space: pre;
                overflow: auto;
                padding: 0px;
                line-height: 19px;
                font-size: 12px;
                color: #898989;
            }

            strong {
                color: #666
            }

            .none {
                display: none;
            }

            fieldset {
                width: 800px;
                margin: 5px 0 10px;
                padding: 10px 10px 20px 10px;
                -moz-border-radius: 5px;
                -khtml-border-radius: 5px;
                -webkit-border-radius: 5px;
                border-radius: 5px;
                border-radius: 0 0 0 15px;
                border: 3px solid #39f;
            }

            fieldset:hover {
                border-color: #bbb;
            }

            fieldset legend {
                color: #777;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                display: block;
                text-shadow: 1px 1px 1px #fff;
                min-width: 90px;
                padding: 0 3px 0 3px;
                border: 1px solid #95abff;
                text-align: center;
                line-height: 30px;
            }

            fieldset .line {
                border-bottom: 1px solid #e5e5e5;
                padding-bottom: 15px;
            }
        </style>


        <script type="text/javascript">
            jQuery(document).ready(function($) {


                $(".toggle").click(function() {
                    $(this).next().slideToggle('normal')
                });


            });
        </script>


        <form action="#" method="post" enctype="multipart/form-data" name="psn_api_form" id="psn_api_form">


            <div class="wrap">


                <div id="icon-options-general" class="icon32"><br></div>


                <h2>PSN API设置</h2><br>


                <fieldset>


                    <legend class="toggle">插件配置</legend>


                    <div>


                        <table width="100%" border="1" class="otakutable">
                            <tr>
                                <td>
                                    <h3>接口配置</h3>
                                    <hr>
                                </td>
                                <td></td>
                            </tr>

                            <tr>
                                <td>API接口:</td>
                                <td><label><input type="text" name="<?php echo $this->api_base ?>" rows="1" style="width:410px;" value="<?php echo ($options[$this->api_base]); ?>"></label></td>
                            </tr>

                            <tr>
                                <td>邀请码:</td>
                                <td><label><input type="text" name="<?php echo $this->azimiao_invitecode ?>" rows="1" style="width:410px;" value="<?php echo ($options[$this->azimiao_invitecode]); ?>"></label></td>
                            </tr>
                            <tr>
                                <td>
                                    <h3>PSN信息</h3>
                                    <hr>
                                </td>
                                <td></td>
                            </tr>

                            <tr>
                                <td>账号ID（数字）：</td>
                                <td><label><input type="number" name="<?php echo $this->psn_account_id ?>" rows="1" style="width:410px;" value="<?php echo ($options[$this->psn_account_id]); ?>"></label></td>
                            </tr>
                           
                            <tr>
                                <td>NPSSO：</td>
                                <td><label><input type="text" name="<?php echo $this->psn_account_npsso ?>" rows="1" style="width:410px;" value="<?php echo ($options[$this->psn_account_npsso]); ?>"></label></td>
                            </tr>

                            <tr>
                                <td>
                                    <h3>保存的Token</h3>
                                    <hr>
                                </td>
                                <td></td>
                            </tr>

                            <tr>
                                <td>Token：</td>
                                <td><label><input type="text" name="<?php echo $this->psn_account_token ?>" rows="1"  style="width:410px;" value="<?php echo ($options[$this->psn_account_token]); ?>" disabled="disabled" ></label></td>
                            </tr>
                           
                            <tr>
                                <td>过期时间：</td>
                                <td><label><input type="text" name="<?php echo $this->psn_account_token_expire ?>" style="width:410px;" rows="1" value="<?php echo ($options[$this->psn_account_token_expire]); ?>" disabled="disabled"></label></td>
                            </tr>
                            <tr>
                        </table>
                    </div>


                </fieldset>

              

                <!-- 提交按钮 -->
                <p class="submit">
                    <input type="submit" name="psn-page-save" value="保存信息" /> &nbsp;
                    <input type="submit" name="psn-page-clear" value="清空Token" />
                </p>

                <fieldset>
                    <legend class="toggle">Bug反馈与联系作者</legend>
                    <div>
                        <table width="800" border="1" class="otakutable">
                            <tr>
                                <td>邮箱</td>
                                <td><label><a href="mailto:admin@azimiao.com" target="_blank">admin@azimiao.com</a></label></td>
                            </tr>
                            <tr>
                                <td>博客</td>
                                <td><label><a href="//www.azimiao.com" target="_blank">梓喵出没(www.azimiao.com)</a></label></td>
                            </tr>
                            <tr>
                                <td>交流</td>
                                <td><label><a href="//jq.qq.com/?_wv=1027&k=57B5rBh" target="_blank">梓喵出没博客交流群</a></label></td>
                            </tr>
                            <tr>
                                <td>声明</td>
                                <td>此插件允许修改自用，禁止二次分发。禁止将此插件内任意代码、样式集成至其他项目。</td>
                            </tr>
                            <tr>
                                <td>版本</td>
                                <td><a href="https://github.com/Azimiao/" target="_blank"><?php echo $this->plugin_version ?></a></td>
                            </tr>
                        </table>
                    </div>
                </fieldset>

            </div>
            <input type="hidden" id="<?php echo $this->nonceflagName ?>" name="<?php echo $this->nonceflagName ?>" value="<?php echo wp_create_nonce($this->nonceflag); ?>" />
        </form>

    <?php
    }

    public function RefreshToken(){
        die();
    }
    public function GetGameList()
    {
        // //wp-ajax 处理方法
        // $options = $this->getOption();
        // $offset = intval($_GET["offset"] ?? 0);
        // $limit =  intval($_GET["limit"] ?? 10);

        // //When Token is null,try get token
        // if($options[$this->psn_account_token] === "" || $options[$this->psn_account_token_expire] <= time()){
            
        //     $k = array(
        //         "auth_key" => $options[$this->azimiao_invitecode],
        //         "npsso" => $options[$this->psn_account_npsso],
        //     );
            

        //     $fullUrl = $options[$this->api_base] . "/token?" . http_build_query($k);

        //     $content = PSNAPI_WPNetwork::http_get_contents($fullUrl,15);

        //     $k = json_decode($content);

        //     if($k == null || !isset($k->code)){
        //         $options[$this->psn_account_token] = "";
        //         update_option($this->optionName, $options);
        //         echo $k;
        //         die();
        //     }
        //     if(!isset($k->data)){
        //         echo $k;
        //         die();
        //     }



        //     if(intval($k->code) != 200 || !isset($k->data->access_token)){
        //         $options[$this->psn_account_token] = "";
        //         update_option($this->optionName, $options);
        //         header("content-type:application/json");
        //         echo $k;
        //         die();
        //     }

        //     $options[$this->psn_account_token] = $k->data->access_token ?? "";
        //     $options[$this->psn_account_token_expire] = intval((intval($k->data->expires ?? 0) + time() - 60));

        //     update_option($this->optionName, $options);
        // }

        // $gameListParams = array(
        //     "auth_key" => $options[$this->azimiao_invitecode],
        //     "npsso" => $options[$this->psn_account_npsso],
        //     "offset"=>$offset,
        //     "limit"=>$limit
        // );

        // $requestBody = array(
        //     "access_token"=>$options[$this->psn_account_token]
        // );

        // $fullGameListUrl = $options[$this->api_base] . "/trophy?" . http_build_query($gameListParams);

        // $t  = PSNAPI_WPNetwork::http_post_contents($fullGameListUrl,15,array('Content-Type' => 'application/json; charset=utf-8'),json_encode($requestBody));

        // if($t == null || $t === ""){
        //     $options[$this->psn_account_token] = "";
        //     update_option($this->optionName, $options);
        //     echo $t;
        //     die();
        // }else{
        //     header("content-type:application/json");
        //     echo $t;
        //     die();
        // }
    }

    public function GetTrophyList()
    {
        //wp-ajax 处理方法
        $options = $this->getOption();
        $offset = intval($_GET["offset"] ?? 0);
        $limit =  intval($_GET["limit"] ?? 10);

        // error_log("judge token time:" . $options[$this->psn_account_token_expire] . "-" . time());

        header('Access-Control-Allow-Origin: *');
        header("content-type:application/json");

        if($options[$this->psn_account_token] == "" || $options[$this->psn_account_token_expire] <= time()){
           
            $k = array(
                "auth_key" => $options[$this->azimiao_invitecode],
                "npsso" => $options[$this->psn_account_npsso],
            );

            $fullUrl = $options[$this->api_base] . "/token?" . http_build_query($k);
            $content = PSNAPI_WPNetwork::http_get_contents($fullUrl,15);
            $k = json_decode($content);
            if($k == null || !isset($k->code)){
                error_log("trophy:code null" . $content);
                $options[$this->psn_account_token] = "";
                $options[$this->psn_account_token_expire] = -1;
                update_option($this->optionName, $options);


                echo $content;
                die();
            }

            if(intval($k->code) != 200 || !isset($k->data->access_token)){
                error_log("trophy:token null or code != 200");
                error_log("k data is null ?" . isset($k->data->access_token));
                $options[$this->psn_account_token] = "";
                $options[$this->psn_account_token_expire] = -1;
                update_option($this->optionName, $options);
                echo $content;
                die();
            }

            $options[$this->psn_account_token] = $k->data->access_token;
            $options[$this->psn_account_token_expire] = intval((intval($k->data->expires ?? 0) + time() - 60));

            update_option($this->optionName, $options);
        }


        $gameListParams = array(
            "auth_key" => $options[$this->azimiao_invitecode],
            "npsso" => $options[$this->psn_account_npsso],
            "offset"=>$offset,
            "limit"=>$limit
        );


        $requestBody = array(
            "access_token"=>$options[$this->psn_account_token]
        );

        $fullGameListUrl = $options[$this->api_base] . "/trophy?" . http_build_query($gameListParams);
        

        $t  = PSNAPI_WPNetwork::http_post_contents($fullGameListUrl,15,array('Content-Type' => 'application/json; charset=utf-8'),json_encode($requestBody));
        
        if($t == null || $t == ""){
            error_log("get trophy result error!");
            $options[$this->psn_account_token] = "";
            $options[$this->psn_account_token_expire] = -1;
            update_option($this->optionName, $options);
            echo $t;
            die();
        }else{
            //todo: 判断 Token 失效并刷新 token
            echo $t;
            die();
        }
    }

}
new Azimiao_PSN_Api();
?>