<?php
namespace Controllers;
use Database\Database;

class UserController extends Database {

    /**
     * By receiving a token as input, this function retrieves the user ID 
     *      associated with this token from the users table in the database.
     *      First, a SQL query is created to select the user ID with the token.
     *      Then, using the executeStatement function, this query is executed
     *      and the result obtained is saved in a statement.
     *      Then, from the finalized result, the first row (the only row) is retrieved using
     *      fetch_assoc and the corresponding user ID is returned.
     *
     * @param   string  $token  user token
     *
     * @return  int          id of the user
     */
    public function getIdByToken($token) {
        $sql = "SELECT id FROM users WHERE token = ?";
        $params = [
            $token
        ];
        $stmt = $this->executeStatement($sql, $params);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        return $row['id'];
    }
    
    /**
     * This function checks if the user whose token submitted the request
     *      has access to a particular quote based on real authentication
     *
     * @param   [type]  $quote_id  Indicates the quote ID we want to check if the current user has access to.
     *
     * @return  [type]             if $row is null == false else $row is not null == true
     */
    public function hasAccess($quote_id) {
        $authController = new AuthController();
        $token = $authController->getToken();
        $user_id = $this->getIdByToken($token);
        $sql = "SELECT 1 FROM quotes WHERE id = ? AND user_id = ?";
        $params = [
            $quote_id,
            $user_id
        ];
        $stmt = $this->executeStatement($sql, $params);
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        if(is_null($row)) {
            return false;
        }else{
            return true;
        }
    }

    /**
     * This function is used in the user registration process for the system
     */
    public function register() {
        if(!array_key_exists('email', $_POST)) {
            $response = [
                'status' => 'error',
                'message' => 'please send your email address with name email',
            ];
            echo json_encode($response);
            die;
        }
        $email = $this->sanitizeInput($_POST['email']);
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = [
                'status' => 'error',
                'message' => 'please send your email address with name email',
            ];
            echo json_encode($response);
            die;    
        }
        $emailArray = explode('@', $email);
        $name = $emailArray[0];
        $token = bin2hex(random_bytes(32));
        $sql = "INSERT INTO users (name, emaile, token) VALUES (?, ?, ?)";
        $params = [
            $name,
            $email,
            $token          
        ];
        $stmt = $this->executeStatement($sql, $params);
        if($stmt->affected_rows == 1) {
            EmailController::send($email, 'token', $token);
            $response = [
                'status' => 'ok',
                'message' => 'please check your inbox',
            ];
            echo json_encode($response);
        }
    }
}