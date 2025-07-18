<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
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

    // List semua task
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $tasks = $this->db->select("tbl_tasks", "*");
        return $this->view->render($response, 'task/list.twig', ['tasks' => $tasks]);
    }

    // Tampilkan form create
    public function createForm(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->view->render($response, 'task/create.twig');
    }

    // Simpan task baru
    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();

        $this->db->insert("tbl_tasks", [
            "title" => $data["title"] ?? '',
            "description" => $data["description"] ?? '',
            "created_at" => date('Y-m-d'),
            "deadline" => $data["deadline"] ?? null,
            "tag" => $data["tag"] ?? '',
            "category" => $data["category"] ?? '',
            "status" => $data["status"] ?? 'NEW',
            "assignees" => $data["assignees"] ?? ''
        ]);

        return $response->withHeader('Location', '/tasks')->withStatus(302);
    }

    // Tampilkan form edit
    public function editForm(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $task = $this->db->get("tbl_tasks", "*", ["id" => $args['id']]);
        return $this->view->render($response, 'task/edit.twig', ['task' => $task]);
    }

    // Proses update
    public function update(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $data = $request->getParsedBody();

        $this->db->update("tbl_tasks", [
            "title" => $data["title"] ?? '',
            "description" => $data["description"] ?? '',
            "deadline" => $data["deadline"] ?? null,
            "tag" => $data["tag"] ?? '',
            "category" => $data["category"] ?? '',
            "status" => $data["status"] ?? 'TODO',
            "assignees" => $data["assignees"] ?? ''
        ], ["id" => $args['id']]);

        return $response->withHeader('Location', '/tasks')->withStatus(302);
    }

    // Hapus task
    public function delete(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $this->db->delete("tbl_tasks", ["id" => $args['id']]);
        return $response->withHeader('Location', '/tasks')->withStatus(302);
    }

    // Detail task
    public function detail(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $task = $this->db->get("tbl_tasks", "*", ["id" => $args['id']]);
        return $this->view->render($response, 'task/detail.twig', ['task' => $task]);
    }
}
