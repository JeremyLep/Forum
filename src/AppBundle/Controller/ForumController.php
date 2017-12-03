<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ForumController extends Controller
{


    public function indexAction()
    {
        return $this->render('AppBundle:Forum:index.html.twig');
    }

    public function discussionAction()
    {
        return $this->render('AppBundle:Forum:discussion.html.twig');
    }

}
