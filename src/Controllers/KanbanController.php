<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Medoo\Medoo;

class KanbanController
{
    protected $view;
    protected $db;

    public function __construct(Twig $view, Medoo $db)
    {
        $this->view = $view;
        $this->db = $db;
    }

    // Halaman dashboard utama
    public function index(Request $request, Response $response, $args): Response
    {
        $statuses = $this->db->select('tbl_statuses', '*');
        $tasks = $this->db->select('tbl_tasks', '*');

        $groupedTasks = [];
        foreach ($statuses as $status) {
            $groupedTasks[$status['name']] = array_filter($tasks, function ($task) use ($status) {
                return $task['status_id'] == $status['id'];
            });
        }

        return $this->view->render($response, 'dashboard.twig', [
            'statuses' => $statuses,
            'grouped_tasks' => $groupedTasks,
        ]);
    }

    // Tambahkan status/board baru
    public function storeStatus(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();
        $statusName = trim($data['status']);

        if (!$statusName) {
            $_SESSION['error'] = 'Nama status wajib diisi.';
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }

        $this->db->insert('tbl_statuses', ['name' => $statusName]);

        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    public function edit(Request $request, Response $response, $args): Response
{
    $id = $args['id'];

    // Ambil data task
    $task = $this->db->get('tbl_tasks', '*', ['id' => $id]);

    if (!$task) {
        $_SESSION['error'] = 'Task tidak ditemukan.';
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    // Ambil semua status
    $statuses = $this->db->select('tbl_statuses', ['id', 'name']);

    // Ambil semua tim (untuk tag)
    $tags = $this->db->select('tbl_teams', ['id', 'name']);

    // Ambil anggota berdasarkan tag (team_id)
    $assignees = $this->db->select('tbl_members', ['id', 'name'], [
        'team_id' => $task['tag']
    ]);

    // Decode assignees yang disimpan sebagai JSON
    $selectedAssignees = json_decode($task['assignees'], true) ?? [];

    return $this->view->render($response, 'kanban/edit.twig', [
        'task' => $task,
        'tbl_statuses' => $statuses,
        'tags' => $tags,
        'assignees' => $assignees,
        'selectedAssignees' => $selectedAssignees
    ]);
}

public function update(Request $request, Response $response, $args): Response
{
    $id = $args['id'];
    $data = $request->getParsedBody();

    // Validasi sederhana
    if (empty($data['title']) || empty($data['description']) || empty($data['status'])) {
        $_SESSION['error'] = 'Field wajib tidak boleh kosong.';
        return $response->withHeader('Location', "/kanban/edit/$id")->withStatus(302);
    }

    // Simpan assignees sebagai JSON array
    $assignees = $data['assignees'] ?? [];
    $encodedAssignees = json_encode($assignees);

    // Update ke database
    $this->db->update('tbl_tasks', [
        'title' => $data['title'],
        'description' => $data['description'],
        'created_at' => $data['created_at'],
        'deadline' => $data['deadline'],
        'tag' => $data['tag'],
        'category' => $data['category'],
        'status_id' => $data['status'],
        'assignees' => $encodedAssignees
    ], ['id' => $id]);

    $_SESSION['success'] = 'Task berhasil diperbarui.';
    return $response->withHeader('Location', '/dashboard')->withStatus(302);
}


    // Simpan task baru
    public function store(Request $request, Response $response, $args): Response
    {
        $data = $request->getParsedBody();

        if (empty($data['title']) || empty($data['created_at']) || empty($data['deadline']) || empty($data['description']) || empty($data['status'])) {
            $_SESSION['error'] = 'Semua field wajib diisi.';
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }

        $statusExists = $this->db->has('tbl_statuses', ['id' => $data['status']]);
        if (!$statusExists) {
            $_SESSION['error'] = 'Status tidak valid.';
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }

        $this->db->insert('tbl_tasks', [
            'title' => $data['title'],
            'category' => $data['category'] ?? 'sedang',
            'created_at' => $data['created_at'],
            'deadline' => $data['deadline'],
            'description' => $data['description'],
            'tag' => $data['tag'] ?? '',
            'status_id' => $data['status'],
            'assignees' => json_encode(explode(',', $data['assignees'] ?? '[]')),
            'team_id' => $data['team_id'] ?? null
        ]);

        $_SESSION['success'] = 'Task berhasil ditambahkan.';
        return $response->withHeader('Location', '/dashboard')->withStatus(302);
    }

    // Drag & Drop: Update status task via AJAX
public function updateStatus(Request $request, Response $response): Response
{
    $body = $request->getBody()->getContents();
    $data = json_decode($body, true);

    $taskId = $data['id'] ?? null;
    $statusName = $data['status'] ?? null;

    if (!$taskId || !$statusName) {
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(400)
                        ->write(json_encode(['success' => false, 'message' => 'ID atau status tidak lengkap']));
    }

    // Cari ID status dari nama
    $status = $this->db->get('tbl_statuses', 'id', ['name' => $statusName]);

    if (!$status) {
        return $response->withHeader('Content-Type', 'application/json')
                        ->withStatus(404)
                        ->write(json_encode(['success' => false, 'message' => 'Status tidak ditemukan']));
    }

    // Update status_id task
    $result = $this->db->update('tbl_tasks', ['status_id' => $status], ['id' => $taskId]);

    if ($result->rowCount() > 0) {
        return $response->withHeader('Content-Type', 'application/json')
                        ->write(json_encode(['success' => true]));
    }

    return $response->withHeader('Content-Type', 'application/json')
                    ->write(json_encode(['success' => false, 'message' => 'Tidak ada data yang diubah']));
}


    // Tampilkan semua task berdasarkan nama status
    public function listByStatus(Request $request, Response $response, $args): Response
    {
        $statusName = urldecode($args['status']);
        $status = $this->db->get('tbl_statuses', '*', ['name' => $statusName]);

        if (!$status) {
            $_SESSION['error'] = 'Status tidak ditemukan.';
            return $response->withHeader('Location', '/dashboard')->withStatus(302);
        }

        $tasks = $this->db->select('tbl_tasks', '*', ['status_id' => $status['id']]);

        return $this->view->render($response, 'task_list_by_status.twig', [
            'status' => $statusName,
            'tasks' => $tasks,
            'session' => $_SESSION
        ]);
    }
}
