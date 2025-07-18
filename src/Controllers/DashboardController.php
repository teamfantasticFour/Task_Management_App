<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Medoo\Medoo;

class DashboardController
{
    protected $view;
    protected $db;

    public function __construct(Twig $view, Medoo $db)
    {
        $this->view = $view;
        $this->db = $db;
    }

    // Halaman utama dashboard
    public function index(Request $request, Response $response): Response
    {
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        // Ambil semua task dan kelompokkan berdasarkan status
        $tasks = $this->db->select("tbl_tasks", "*");

        $grouped = [];
        foreach ($tasks as $task) {
            $grouped[$task['status']][] = $task;
        }

        return $this->view->render($response, 'dashboard.twig', [
            'user' => $user,
            'grouped_tasks' => $grouped
        ]);
    }
}
