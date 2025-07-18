<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Medoo\Medoo;
use Slim\Psr7\Response as Psr7Response;

class KanbanController
{
    protected $view;
    protected $db;

    public function __construct(Twig $view, Medoo $db)
    {
        $this->view = $view;
        $this->db = $db;
    }

    // Tampilkan semua task yang dikelompokkan berdasarkan status
    public function index(Request $request, Response $response): Response
    {
        $tasks = $this->db->select("tbl_tasks", "*");

        $grouped = [];
        foreach ($tasks as $task) {
            $grouped[$task['status']][] = $task;
        }

        return $this->view->render($response, 'dashboard.twig', [
            'grouped_tasks' => $grouped
        ]);
    }

    // Tampilkan form buat task baru
    public function showCreateForm(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'kanban_create.twig');
    }

    // Simpan task baru ke database
    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        $this->db->insert("tbl_tasks", [
            "title" => $data["title"],
            "description" => $data["description"],
            "created_at" => $data["created_at"],
            "deadline" => $data["deadline"],
            "tag" => $data["tag"],
            "category" => $data["category"],
            "status" => $data["status"],
            "assignees" => $data["assignees"] ?? null
        ]);

        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }
    // Tambahkan method ini di KanbanController.php
public function updateStatus(Request $request, Response $response): Response
{
    $parsedBody = $request->getParsedBody();
    
    // Jika JSON dikirim sebagai raw input
    if (empty($parsedBody)) {
        $parsedBody = json_decode(file_get_contents('php://input'), true);
    }

    $id = $parsedBody['id'] ?? null;
    $status = $parsedBody['status'] ?? null;

    if (!$id || !$status) {
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(400)
                        ->withJson(['success' => false, 'message' => 'Data tidak lengkap.']);
    }

    $updated = $this->db->update('tbl_tasks', [
        'status' => $status
    ], [
        'id' => $id
    ]);

    if ($updated->rowCount() > 0) {
        return $response->withHeader('Content-Type', 'application/json')
                        ->withJson(['success' => true]);
    } else {
        return $response->withHeader('Content-Type', 'application/json')
                        ->withJson(['success' => false, 'message' => 'Task tidak ditemukan atau status tidak berubah.']);
    }
}

}