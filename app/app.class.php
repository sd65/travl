<?php
include "template.php";
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

    function tplBasicSet($funcName)
    {
        $tpl = & new Template();
        $tpl->set("method", $funcName);
        $tpl->set("class", __CLASS__);
        $tpl->set("url", $this->conf->url);
        $tpl->set("title", $this->conf->title);
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

        $tpl = $this->tplBasicSet(__FUNCTION__);
        $tpl->set("articles", $this->data->getArticles($vars['people']));
        $tpl->set("randomImg", $this->data->getRandomImg());
        $tpl->set("peoples", $this->data->getPeople());
        $tpl->set("people", $vars['people']);

        $tpl->display("tpl/app/index.savant");
    }

    function view($vars)
    {
        $tpl = $this->tplBasicSet(__FUNCTION__);
        $tpl->set("article", $this->data->getArticle($vars['people'], $vars['id']));
        $tpl->set("peoples", $this->data->getPeople());
        $tpl->set("people", $vars['people']);

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
            $tpl = $this->tplBasicSet(__FUNCTION__);
            $tpl->set("randomImg", $this->data->getRandomImg());
            $tpl->set("peoples", $this->data->getPeople());
            $tpl->set("people", $vars['people']);

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
            $tpl = $this->tplBasicSet(__FUNCTION__);
            $tpl->set("randomImg", $this->data->getRandomImg());
            $tpl->set("peoples", $this->data->getPeople());
            $tpl->set("people", $vars['people']);

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
        $tpl = $this->tplBasicSet(__FUNCTION__);
        $tpl->set("peoples", $this->data->getPeople());

        if (!isset($_SESSION['validUser']))
        {
            exit(header('Location: login?people=' . $vars['people']));
        }
        $tpl->set("randomImg", $this->data->getRandomImg());
        $tpl->set("peoples", $this->data->getPeople());
        $tpl->set("people", $_SESSION['validUser']);

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
