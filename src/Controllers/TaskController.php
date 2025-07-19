<?php

namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Medoo\Medoo;

class TaskController
{
    protected $view, $db;

    public function __construct(Twig $view, Medoo $db)
    {
        $this->view = $view;
        $this->db = $db;
    }

    public function index(Request $request, Response $response): Response
{
    $tasks = $this->db->select("tbl_tasks", "*");
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $this->view->render($response, 'task/list.twig', [
        'tasks' => $tasks,
        'flash' => $flash
    ]);
}

    public function createForm(Request $request, Response $response, array $args): Response
    {
        $statuses = $this->db->select("tbl_statuses", "*");
        $teams = $this->db->select("tbl_teams", ["name"]); // ambil hanya kolom nama tim

        return $this->view->render($response, 'task/create.twig', [
            'tbl_statuses' => $statuses,
            'teams' => $teams
        ]);
    }

    public function store(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        $this->db->insert("tbl_tasks", [
            "title" => $data["title"] ?? '',
            "description" => $data["description"] ?? '',
            "created_at" => $data["created_at"] ?? date('Y-m-d'),
            "deadline" => $data["deadline"] ?? null,
            "tag" => $data["tag"] ?? '',
            "category" => $data["category"] ?? '',
            "status_id" => $data["status"] ?? ''
        ]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Task berhasil ditambahkan'];
        return $response->withHeader('Location', '/tasks')->withStatus(302);
    }

 public function detail(Request $request, Response $response, array $args): Response
{
    $id = $args['id'];
    $task = $this->db->get("tbl_tasks", "*", ["id" => $id]);

    if (!$task) {
        $response->getBody()->write("Task tidak ditemukan");
        return $response->withStatus(404);
    }

   return $this->view->render($response, 'task/detail.twig', [
    'task' => $task
]);
}


    public function editForm(Request $request, Response $response, array $args): Response
{
    $task = $this->db->get("tbl_tasks", "*", ["id" => $args['id']]);
    if (!$task) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Task tidak ditemukan.'];
        return $response->withHeader('Location', '/tasks')->withStatus(302);
    }

    $statuses = $this->db->select("tbl_statuses", "*");
    $teams = $this->db->select("tbl_teams", ["name"]);

    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    return $this->view->render($response, 'task/edit.twig', [
        'task' => $task,
        'tbl_statuses' => $statuses,
        'teams' => $teams,
        'flash' => $flash
    ]);
}

    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        $this->db->update("tbl_tasks", [
            "title" => $data["title"] ?? '',
            "description" => $data["description"] ?? '',
            "created_at" => $data["created_at"] ?? date('Y-m-d'),
            "deadline" => $data["deadline"] ?? null,
            "tag" => $data["tag"] ?? '',
            "category" => $data["category"] ?? '',
            "status_id" => $data["status"] ?? ''
        ], ["id" => $args['id']]);

        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Task berhasil diperbarui'];
        return $response->withHeader('Location', '/tasks')->withStatus(302);
    }

    public function delete(Request $request, Response $response, array $args): Response
{
    $taskId = $args['id'] ?? null;

    if (!$taskId) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'ID tidak valid.'];
        return $response->withHeader('Location', '/tasks')->withStatus(302);
    }

    // Periksa apakah task ada
    $task = $this->db->get("tbl_tasks", "*", ["id" => $taskId]);
    if (!$task) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'Task tidak ditemukan.'];
        return $response->withHeader('Location', '/tasks')->withStatus(302);
    }

    // Hapus
    $this->db->delete("tbl_tasks", ["id" => $taskId]);

    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Task berhasil dihapus'];
    return $response->withHeader('Location', '/tasks')->withStatus(302);
}
}