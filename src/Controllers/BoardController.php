<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;
use Medoo\Medoo;

class BoardController
{
    protected $view, $db;

    public function __construct(Twig $view, Medoo $db)
    {
        $this->view = $view;
        $this->db = $db;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $boards = $this->db->select("boards", "*", ["ORDER" => ["id" => "DESC"]]);
        return $this->view->render($response, 'board/index.twig', [
            'boards' => $boards
        ]);
    }

    public function store(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $data = $request->getParsedBody();
        $this->db->insert("boards", [
            "title" => $data["title"],
            "created_at" => date('Y-m-d')
        ]);

        return $response->withHeader('Location', '/boards')->withStatus(302);
    }

    public function delete(ServerRequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $this->db->delete("boards", ["id" => $args["id"]]);
        return $response->withHeader('Location', '/boards')->withStatus(302);
    }
}
