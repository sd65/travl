<?php
include "app/savant/Savant3.php";
include "data.class.php";

class app
{

    //===============================================
    // Frameworks
    //===============================================

    private $conf;

    function __construct($conf)
    {
        $this->conf = $conf;
        $this->data = new Data($conf);
    }

    function tplBasicConfig($funcName)
    {
        $tpl = new Savant3();
        $tpl->assign("method", $funcName);
        $tpl->assign("class", __CLASS__);
        $tpl->assign("url", $this->conf->url);
        $tpl->assign("title", $this->conf->title);
        return $tpl;
    }

    //===============================================
    // WebApp
    //===============================================

    function index($vars)
    {
        if (!isset($vars['people']))
        {
            $vars['people'] = "commun";
        }

        if ($vars['people'] != "commun") 
        {
            $flag = $this->data->checkAuth($vars['people']);

            if (!$flag) 
            {
                exit(header('Location: login?people=' . $vars['people']));
            }
        }

        $tpl = $this->tplBasicConfig(__FUNCTION__);
        $tpl->assign("articles", $this->data->getArticles($vars['people']));
        $tpl->assign("randomImg", $this->data->getRandomImg());
        $tpl->assign("peoples", $this->data->getPeople());
        $tpl->assign("people", $vars['people']);

        $tpl->display("tpl/app/index.savant");
    }

    function view($vars)
    {
        $tpl = $this->tplBasicConfig(__FUNCTION__);
        $tpl->assign("article", $this->data->getArticle($vars['people'], $vars['id']));
        $tpl->assign("peoples", $this->data->getPeople());
        $tpl->assign("people", $vars['people']);

        $tpl->display("tpl/app/view.savant");
    }

    function login($vars)
    {
        if (isset($vars['people'], $vars['password']))
        {
            $this->data->login($vars['people'], $vars['password']);
            exit(header('Location: index?people=' . $vars['people']));
        }
        else
        {
            $tpl = $this->tplBasicConfig(__FUNCTION__);
            $tpl->assign("randomImg", $this->data->getRandomImg());
            $tpl->assign("peoples", $this->data->getPeople());
            $tpl->assign("people", $vars['people']);

            $tpl->display("tpl/app/login.savant");
        }
    }

    function logout($vars)
    {
        $this->data->logout();
        exit(header('Location: index'));
    }

    function changePassword($vars)
    {
        if (isset($vars['people'], $vars['newPassword']))
        {
            $this->data->changePassword($vars['people'], $vars['newPassword']);
            exit(header('Location: index?people=' . $vars['people']));
        }
        else
        {
            $tpl = $this->tplBasicConfig(__FUNCTION__);
            $tpl->assign("randomImg", $this->data->getRandomImg());
            $tpl->assign("peoples", $this->data->getPeople());
            $tpl->assign("people", $vars['people']);

            $flag = $this->data->checkAuth($vars['people']);
            if (!$flag) 
            {
                exit(header('Location: login?people=' . $vars['people']));
            }

            $tpl->display("tpl/app/changePassword.savant");
        }
    }

    function write($vars)
    {
        $tpl = $this->tplBasicConfig(__FUNCTION__);
        $tpl->assign("peoples", $this->data->getPeople());

        if (!isset($_SESSION['validUser'])) 
        {
            exit(header('Location: login?people=' . $vars['people']));
        }
        $tpl->assign("randomImg", $this->data->getRandomImg());
        $tpl->assign("peoples", $this->data->getPeople());
        $tpl->assign("people", $_SESSION['validUser']);

        $tpl->display("tpl/app/write.savant");
    }

    function upload($vars)
    {
        if (isset($vars['file'], $_SESSION['validUser']))
        {
            $this->data->uploadImage($vars['file'], $_SESSION['validUser']);
        }
    }

    function getRandomImg($vars)
    {
        echo $this->data->getRandomImg();    
    }

    function listImages($vars)
    {
        if (isset($_SESSION['validUser']))
        {
            $this->data->listImages($_SESSION['validUser']);
        }
    }

    function deleteImage($vars)
    {
        if (isset($_SESSION['validUser'], $vars['src']))
        {
            $this->data->deleteImage($vars['src']);
        }
    }

    function submitWrite($vars)
    {
        if (isset($vars['toPeople'], $vars['title'], $vars['meta'], $vars['content'], $vars['picture']))
        {
            $this->data->submitWrite($vars['toPeople'], $vars['title'][0], $vars['meta'][0], $vars['content'][0], $vars['picture']);
            echo $_SESSION['validUser'];
        }
    }

}
 // End of Class
