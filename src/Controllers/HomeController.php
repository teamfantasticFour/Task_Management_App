<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class HomeController
{
    protected $view;

    public function __construct(Twig $view)
    {
        $this->view = $view;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        // ✅ Pastikan session aktif
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // ✅ Render terlebih dahulu
        $response = $this->view->render($response, 'home.twig');

        // ✅ Baru setelah itu hapus flash message
        unset($_SESSION['success_message']);

        return $response;
    }
}
