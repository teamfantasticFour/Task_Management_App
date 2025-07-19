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

    public function index(Request $request, Response $response): Response
    {
        // Autentikasi
        $user = $_SESSION['user'] ?? null;
        if (!$user) {
            return $response->withHeader('Location', '/login')->withStatus(302);
        }

        // Ambil semua status (board) dari tbl_statuses
        $statuses = $this->db->select("tbl_statuses", "*");

        // Ambil semua task dari tbl_tasks
        $tasks = $this->db->select("tbl_tasks", "*");

        // Kelompokkan task berdasarkan status_id
        $grouped = [];
        foreach ($statuses as $status) {
            $grouped[$status['id']] = [
                'status' => $status,
                'tasks' => []
            ];
        }

        foreach ($tasks as $task) {
            $statusId = $task['status_id'] ?? null;
            if ($statusId && isset($grouped[$statusId])) {
                $grouped[$statusId]['tasks'][] = $task;
            } else {
                // Jika tidak ada status_id yang cocok, masukkan ke "tanpa status"
                $grouped[0]['tasks'][] = $task;
            }
        }

        // Ambil pesan SweetAlert jika ada
        $flash = $_SESSION['sweetalert'] ?? null;
        unset($_SESSION['sweetalert']);

        return $this->view->render($response, 'dashboard.twig', [
            'user' => $user,
            'statuses' => $statuses,
            'grouped_tasks' => $grouped,
            'flash' => $flash,
        ]);
    }
}
