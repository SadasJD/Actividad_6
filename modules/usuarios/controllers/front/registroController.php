<?php

class registroController extends controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $preguntas = [
            '¿Cuál es el nombre de tu primera mascota?',
            '¿Cuál es tu comida favorita?',
            '¿En qué ciudad naciste?',
            '¿Cuál es el segundo nombre de tu padre?',
            '¿Cuál era tu apodo de niño?',
            '¿Cuál es el nombre de tu abuela materna?',
            '¿Cuál es tu película favorita?'
        ];
        $this->view->preguntas = $preguntas;
        $this->view->render('front/registro');
    }

    public function registrar()
    {
        if (empty($_POST['nombre']) || empty($_POST['apellido']) || empty($_POST['email']) || empty($_POST['whatsapp']) || empty($_POST['password']) || empty($_POST['pregunta1']) || empty($_POST['respuesta1']) || empty($_POST['pregunta2']) || empty($_POST['respuesta2'])) {
            $this->view->error = 'Todos los campos son obligatorios';
            $this->view->render('front/registro');
            exit;
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $this->view->error = 'El email no es válido';
            $this->view->render('front/registro');
            exit;
        }

        if (!is_numeric($_POST['whatsapp']) || strlen($_POST['whatsapp']) != 10) {
            $this->view->error = 'El whatsapp debe ser un número de 10 digitos';
            $this->view->render('front/registro');
            exit;
        }

        $usuario = usuarios::getByWhatsapp($_POST['whatsapp']);
        if ($usuario) {
            $this->view->error = 'El whatsapp ya esta registrado';
            $this->view->render('front/registro');
            exit;
        }

        if (empty($_POST['pregunta1']) || empty($_POST['respuesta1']) || empty($_POST['pregunta2']) || empty($_POST['respuesta2'])) {
            $this->view->error = 'Las preguntas y respuestas de seguridad son obligatorias';
            $this->view->render('front/registro');
            exit;
        }

        $usuario = new usuarios();
        $usuario->setNombre($_POST['nombre']);
        $usuario->setApellido($_POST['apellido']);
        $usuario->setEmail($_POST['email']);
        $usuario->setWhatsapp($_POST['whatsapp']);
        $usuario->setPassword(hash('sha256', $_POST['password']));
        $usuario->setRole(2);
        $usuario->setActivo(1);
        $usuario->setFecha_creacion(date('Y-m-d H:i:s'));
        $usuario->setPregunta1($_POST['pregunta1']);
        $usuario->setRespuesta1($_POST['respuesta1']);
        $usuario->setPregunta2($_POST['pregunta2']);
        $usuario->setRespuesta2($_POST['respuesta2']);

        if ($usuario->save()) {
            $this->view->mensaje = 'Usuario registrado correctamente, inicie sesion';
            $this->view->render('front/login');
        } else {
            $this->view->error = 'Error al registrar el usuario';
            $this->view->render('front/registro');
        }
    }
}