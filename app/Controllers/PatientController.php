<?php

namespace App\Controllers;

use App\Models\PatientModel;
use App\Database\Connection;

class PatientController extends BaseController
{
    public function index()
    {
        $model = new PatientModel();
        $pacientes = $model->getAll();
        $this->view('pages/pacientes_lista', ['pacientes' => $pacientes]);
    }

    public function create()
    {
        $this->view('pages/cadastro_paciente');
    }

    public function edit($id = null)
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $userId = $_SESSION['user_id'] ?? $_SESSION['user']['id'] ?? null;
        if (!$userId) {
            header('Location: ' . URL_BASE . '/login');
            exit;
        }

        $pacienteId = $id ?? filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if (!$pacienteId) {
            header('Location: ' . URL_BASE . '/pacientes');
            exit;
        }

        $model = new PatientModel();
        $paciente = $model->find($pacienteId);
        if (!$paciente) {
            header('Location: ' . URL_BASE . '/pacientes');
            exit;
        }

        $this->view('pages/cadastro_paciente', ['paciente' => $paciente]);
    }

    // Método Store atualizado com Tags
    public function store()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $dataNascimento = $_POST['data_nascimento'] ?? null;
        if (empty($dataNascimento)) $dataNascimento = null;

        $dados = [
            'nome' => filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS),
            'cpf' => filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'telefone' => filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_SPECIAL_CHARS),
            'data_nascimento' => $dataNascimento,
            'genero' => filter_input(INPUT_POST, 'genero', FILTER_SANITIZE_SPECIAL_CHARS),
            'origem' => filter_input(INPUT_POST, 'origem', FILTER_SANITIZE_SPECIAL_CHARS),
            'tags' => filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_SPECIAL_CHARS), // CAPTURA DE TAGS
            'nome_responsavel' => filter_input(INPUT_POST, 'nome_responsavel', FILTER_SANITIZE_SPECIAL_CHARS),
            'responsavel_financeiro' => filter_input(INPUT_POST, 'responsavel_financeiro', FILTER_SANITIZE_SPECIAL_CHARS),
            'cep' => filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_SPECIAL_CHARS),
            'logradouro' => filter_input(INPUT_POST, 'logradouro', FILTER_SANITIZE_SPECIAL_CHARS),
            'numero' => filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_SPECIAL_CHARS),
            'complemento' => filter_input(INPUT_POST, 'complemento', FILTER_SANITIZE_SPECIAL_CHARS),
            'bairro' => filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_SPECIAL_CHARS),
            'cidade' => filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_SPECIAL_CHARS),
            'estado' => filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS),
        ];

        if ($dados['nome']) {
            $model = new PatientModel();
            try {
                if ($model->create($dados)) {
                    $_SESSION['success'] = "Paciente cadastrado com sucesso!";
                } else {
                    $_SESSION['error'] = "Erro ao cadastrar paciente.";
                }
            } catch (\Exception $e) {
                $_SESSION['error'] = "Erro: " . $e->getMessage();
            }
        }

        header('Location: ' . URL_BASE . '/pacientes');
        exit;
    }

    // Método Update atualizado com Tags
    public function update()
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        $id = filter_input(INPUT_POST, 'id_paciente', FILTER_SANITIZE_NUMBER_INT);

        $dataNascimento = $_POST['data_nascimento'] ?? null;
        if (empty($dataNascimento)) $dataNascimento = null;

        $dados = [
            'nome' => filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS),
            'cpf' => filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_SPECIAL_CHARS),
            'email' => filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL),
            'telefone' => filter_input(INPUT_POST, 'telefone', FILTER_SANITIZE_SPECIAL_CHARS),
            'data_nascimento' => $dataNascimento,
            'genero' => filter_input(INPUT_POST, 'genero', FILTER_SANITIZE_SPECIAL_CHARS),
            'origem' => filter_input(INPUT_POST, 'origem', FILTER_SANITIZE_SPECIAL_CHARS),
            'tags' => filter_input(INPUT_POST, 'tags', FILTER_SANITIZE_SPECIAL_CHARS), // CAPTURA DE TAGS
            'nome_responsavel' => filter_input(INPUT_POST, 'nome_responsavel', FILTER_SANITIZE_SPECIAL_CHARS),
            'responsavel_financeiro' => filter_input(INPUT_POST, 'responsavel_financeiro', FILTER_SANITIZE_SPECIAL_CHARS),
            'cep' => filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_SPECIAL_CHARS),
            'logradouro' => filter_input(INPUT_POST, 'logradouro', FILTER_SANITIZE_SPECIAL_CHARS),
            'numero' => filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_SPECIAL_CHARS),
            'complemento' => filter_input(INPUT_POST, 'complemento', FILTER_SANITIZE_SPECIAL_CHARS),
            'bairro' => filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_SPECIAL_CHARS),
            'cidade' => filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_SPECIAL_CHARS),
            'estado' => filter_input(INPUT_POST, 'estado', FILTER_SANITIZE_SPECIAL_CHARS),
        ];

        if ($id && $dados['nome']) {
            $model = new PatientModel();
            try {
                if ($model->update($id, $dados)) {
                    $_SESSION['success'] = "Paciente atualizado com sucesso!";
                } else {
                    $_SESSION['error'] = "Não foi possível atualizar os dados.";
                }
            } catch (\Exception $e) {
                $_SESSION['error'] = "Erro ao processar: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "Nome é obrigatório ou ID inválido.";
        }

        header('Location: ' . URL_BASE . '/pacientes');
        exit;
    }
public function apiSearch()
    {
        $termo = filter_input(INPUT_GET, 'term', FILTER_SANITIZE_SPECIAL_CHARS);

        if (!$termo || strlen($termo) < 2) {
            header('Content-Type: application/json');
            echo json_encode([]);
            exit;
        }

        try {
            $patientModel = new PatientModel();
            $resultados = $patientModel->search($termo);

            $json = [];
            foreach ($resultados as $p) {
                $cpf = $p['cpf'] ? $p['cpf'] : 'S/ CPF';
                
                $json[] = [
                    'id' => $p['id_paciente'],
                    'nome' => $p['nome'],
                    'cpf' => $cpf,
                    'label' => $p['nome'] . ' - ' . $cpf,
                    
                    // NOVOS CAMPOS PARA RETORNO
                    'telefone' => $p['telefone'] ?? '',
                    'nome_responsavel' => $p['nome_responsavel'] ?? '',
                    'responsavel_financeiro' => $p['responsavel_financeiro'] ?? ''
                ];
            }

            header('Content-Type: application/json');
            echo json_encode($json);

        } catch (\Exception $e) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Erro interno: ' . $e->getMessage()]);
        }
        exit;
    }
}
