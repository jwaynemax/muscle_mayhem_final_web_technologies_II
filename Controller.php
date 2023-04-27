<?php

require_once './model/Database.php';
require_once 'autoload.php';

class Controller {

    private $action;
    private $db;
    private $twig;

    /**
     * Instantiates a new controller
     */
    public function __construct() {
        $loader = new Twig\Loader\FilesystemLoader('./view');
        $this->twig = new Twig\Environment($loader);
        $this->setupConnection();
        $this->connectToDatabase();
        $this->twig->addGlobal('session', $_SESSION);
        $this->action = $this->getAction();
    }

    /**
     * Initiates the processing of the current action
     */
    public function invoke() {
        switch ($this->action) {
            case 'User_Profile':
                $this->processShowUserProfilePage();
                break;
            case 'Personal_Training':
                $this->processShowPersonalTrainingPage();
                break;
            case 'Logout':
                $this->processLogout();
                break;
            case 'Register':
                $this->processShowRegisterPage();
                break;
            case 'Login':
                $this->processShowLoginPage();
                break;
            case 'Home':
                $this->processShowHomePage();
                break;
            default:
                $this->processShowHomePage();
                break;
        }
    }

    /*     * **************************************************************
     * Process Request
     * ************************************************************* */
    
    /**
     * Process Logout
     */
    private function processShowUserProfilePage() {
        $template = $this->twig->load('user_profile.twig');
        echo $template->render();
    }
    
    /**
     * Process show personal training page
     */
    private function processShowPersonalTrainingPage() {
        $template = $this->twig->load('personal_training.twig');
        echo $template->render();
    }
    
    /**
     * Process Logout
     */
    private function processLogout() {
        $_SESSION = array();
        session_destroy();
        $this->twig->addGlobal('session', $_SESSION);
        $login_message = 'You have been logged out.';
        $template = $this->twig->load('login.twig');
        echo $template->render(['login_message' => $login_message, 'session']);
    }
    
    /**
     * Shows the Register page
     */
    private function processShowRegisterPage() {
        $template = $this->twig->load('register.twig');
        echo $template->render();
    }
    
    /**
     * Shows the Login page
     */
    private function processShowLoginPage() {
        $template = $this->twig->load('login.twig');
        echo $template->render();
    }

    /**
     * Shows the home page
     */
    private function processShowHomePage() {
        $template = $this->twig->load('home.twig');
        echo $template->render();
    }

    /**
     * Gets the action from $_GET or $_POST array
     * 
     * @return string the action to be processed
     */
    private function getAction() {
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($action === NULL) {
            $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($action === NULL) {
                $action = '';
            }
        }
        return $action;
    }
    
    /**
     * Ensures a secure connection and start session
     */
    private function setupConnection() {
        $https = filter_input(INPUT_SERVER, 'HTTPS');
        if (!$https) {
            $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
            $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
            $url = 'https://' . $host . $uri;
            header("Location: " . $url);
            exit();
        }
        session_start();
    }

    /**
     * Connects to the database
     */
    private function connectToDatabase() {
        $this->db = new Database();
        if (!$this->db->isConnected()) {
            $error_message = $this->db->getErrorMessage();
            $template = $this->twig->load('database_error.twig');
            echo $template->render(['error_message' => $error_message]);
            exit();
        }
    }

}
