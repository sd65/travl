<?php
class Data
{

    //===============================================
    // Framework
    //===============================================

    private $conf;

    function __construct($conf)
    {
        $this->conf = $conf;
    }

    //===============================================
    // WebApp
    //===============================================

    function getArticles($people)
    {
        $filenames = scandir("data/$people" , 1);
        rsort($filenames);
        $articles = array();

        foreach ($filenames as $key => $filename)
        {
            $temp = explode(".", $filename);
            $extension = end($temp);

            if ($extension === "article")
            {
                array_push($articles, $this->parseArticle("data/$people/$filename"));
            }
        }
        return $articles;
    }

    function getRandomImg()
    {
        $filenames = scandir("img/static/" , 1);
        sort($filenames);
        unset($filenames[0]);   // Remove "."
        unset($filenames[1]);   // Remove ".."
        return "img/static/" . $filenames[array_rand($filenames)];
    }

    function getArticle($people, $id)
    {
        return $this->parseArticle("data/$people/$id" . '.article');
    }

    function parseArticle($path)
    {
        $file = fopen($path, "r");

        $article = array();
        $article['people'] = basename(dirname($path));
        $article['id'] = basename($path, ".article");
        $article['content'] = "";
        $lineNumber = 0;

        while(!feof($file) && ++$lineNumber)
        {
            $line = fgets($file);

            switch ($lineNumber)
            {
                case 1:
                    $article['title'] = trim(substr($line, 2));
                    break;
                case 2:
                    $article['meta'] = trim(substr($line, 4));
                    break;
                case 3:
                    $article['picture'] = trim(substr($line, 5));
                    break;

                default:
                    $article['content'] .= $line;
                    break;
            }

        }
        fclose($file);

        return $article;
    }

    function getPeople()
    {
        $filenames = scandir('data/' , 1);
        sort($filenames);
        $peoples = array();

        foreach ($filenames as $key => $filename)
        {
            if (($filename === ".") || ($filename === "..") || ($filename === "commun"))
            {
                continue;
            }
            else
            {
                array_push($peoples, $filename);
            }
        }
        return array_merge(array('commun'), $peoples);
    }

    function checkAuth($people)
    {
        if(isset($_SESSION['validUser']))
        {
            if ($_SESSION['validUser'] == $people)
            {
                return true;
            }
        }
        if (file_exists("data/$people/"))
        {
            if (!file_exists("data/$people/password"))
            {
                $_SESSION['validUser'] = $people;
                exit(header('Location: changePassword?people=' . $people));
            }
        }
        return false;
    }

    function login($people, $password)
    {
        $correctHash = file_get_contents("data/$people/password");
        if (password_verify($password, $correctHash))
        {
            $_SESSION['validUser'] = $people;
        }
        else
        {
            session_unset();
        }
    }

    function logout()
    {
        session_unset();
    }

    function changePassword($people, $newPassword)
    {
        $newPasswordhash = password_hash($newPassword, PASSWORD_DEFAULT);
        file_put_contents("data/$people/password", $newPasswordhash);
    }

    function uploadImage($file, $people)
    {
        // Allowed extentions.
        $allowedExts = array("jpeg", "jpg", "png");

        // Get extension.
        $extension = end(explode(".", $file["name"]));

        if ((in_array($extension, $allowedExts)) && ($file["size"] < $this->conf->maxUploadSize )) {

            // Generate new random name.
            $filename = "data/$people/" . sha1(microtime()) . ".jpg";

            // Resize image
            if( $extension == "png")
            {
                $image = imagecreatefrompng($_FILES["file"]["tmp_name"]);
            }
            else
            {
                $image = imagecreatefromjpeg($_FILES["file"]["tmp_name"]);
            }

            $width = 900;
            $height = imagesy($image) * ($width / imagesx($image));

            $newImage = imagecreatetruecolor($width, $height);
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
            imagejpeg($newImage, $filename, 75);

            // Generate response.
            $response = new StdClass;
            $response->link = $filename;
            echo stripslashes(json_encode($response));
        }
    }

    function listImages($people)
    {
        $filenames = scandir("data/$people" , 1);
        sort($filenames);

        $allowedExts = array("gif", "jpeg", "jpg", "png");
        $images = array();

        foreach ($filenames as $filename)
        {
            $temp = explode(".", $filename);
            $extension = end($temp);
            if (in_array($extension, $allowedExts))
            {
                array_push($images, "data/$people/" . $filename);
            }
        }
        echo stripslashes(json_encode($images));
    }

    function deleteImage($path)
    {
        unlink($path);
    }

    function submitWrite($toPeople, $title, $meta, $content, $picture)
    {
        $id = time();
        $content = "# $title \n## $meta \n### $picture\n$content";
        file_put_contents("data/$toPeople/$id.article", $content);
    }

}

// End of Class
