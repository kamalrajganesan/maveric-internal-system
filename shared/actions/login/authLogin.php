<?php

class SessUsers {
    
    public function __construct()
    {
        if (!session_id()) {
            session_start();
        }
    }

    public function SetAgentSession($usr) {
     
        $this->unsetSession();

        $agent['nm'] = $usr["agent_nm"];
        $agent['email'] = $usr["email"];
        $agent['phone'] = $usr["primary_contact"];
        $agent['id'] = $usr["id"];
     
        $_SESSION['user'] = $agent;
        $_SESSION['userType'] = "agent";
    }

    public function SetAdminSession($usr) {
     
        $this->unsetSession();


        $agent['nm'] = $usr["nm"];
        $agent['email'] = $usr["email"];
        $agent['phone'] = $usr["phone"];
        $agent['id'] = "0";
     
        $_SESSION['user'] = $agent;
        $_SESSION['userType'] = "admin";
    }

    public function checkAgentSession() {
        
        if (isset($_SESSION['agent'])) {
            return true;
        } else {
            return false;
        }
    }

    public function unsetSession() {
        
        if (isset($_SESSION['agent'])) {
            unset($_SESSION['agent']);
        }
    }
}

?>