<?php
namespace Controllers;
use Database\Database;
use Traits\SanitizerTrait;
class QuotesController extends Database{
    use SanitizerTrait;

    // Database table name
    protected $table = 'quotes';
    
/**
 * Select all data using the get method
 */
    public function index(){
    $sql = "SELECT quotes.*, users.name AS username FROM $this->table INNER JOIN users ON users.id = quotes.user_id";
        $stmt = $this->executeStatement($sql);
        $rows = $stmt->get_result();
        if($rows->num_rows <= 0) {
            $response = [
                "status" => "error",
                "message" => "no record exists in database"
            ];
        }else{
            $data = [];
            while($row = $rows->fetch_assoc()) {
                $data[] = $row;
            }
        }
        http_response_code(200);
        echo json_encode($data);
    }

    /**
     * Data with condition id Select using the get method
     * 
     * @param   [type]  $params     $params['id' => '$id'];
     *                              $id is the id of the condition to be selected in the database
     */
    public function getQuotes($params) {
        $id = $params['id'];
        $sql = "SELECT * FROM $this->table WHERE id = ?";
        $sql_params = [$id];
        $stmt = $this->executeStatement($sql, $sql_params);
        $row = $stmt->get_result();
        $row = $row->fetch_assoc();
        if(is_null($row)) {
            $response = [
                'status' => 'erroe',
                'message' => 'record not found.'
            ];
        }else{
            $response = $row;
        }
        http_response_code(200);
        echo json_encode($response);
    }

    /**
     * Inserting data using the post method
     */
    public function store() {

        //  Get Token
        $authController = new AuthController();
        $token = $authController->getToken();

        //  Get user id
        $userControllers = new UserController();
        $user_id = $userControllers->getIdByToken($token);

        $data = $this->sanitizeInput($_POST);
        if(array_key_exists('quote', $data) || array_key_exists('author', $data)) {
            $sql = "INSERT INTO $this->table (user_id, quote, author) VALUES (?, ?, ?)";
            $params = [
                $user_id,
                $data['quote'],
                $data['author']
            ];
            $stmt = $this->executeStatement($sql, $params);
            if($stmt->affected_rows == 1) {
                $response = [
                    'status' => 'ok',
                    'quote_id' => $stmt->insert_id,
                    'message' => 'Quote added successfully'
                ];
            }else{
                $response = [
                    'status' => 'error',
                    'message' => 'can not insert new row'
                ];
            } 
        }else{
            $response = [
                'status' => 'error',
                'message' => 'invalid input'
            ];
        }
        echo json_encode($response);
    }

    /**
     * Update data using the PUT method
     */
    public function updateQuotes($id) {
        $id = $this->sanitizeInput($id['id']);
        $put_data = file_get_contents("php://input");
        parse_str($put_data, $data);
        $data = $this->sanitizeInput($data);
        $sql = "UPDATE $this->table SET ";
        $update = [];
        foreach($data as $key => $value) {
            $update [] = "$key = '$value'";
        }
        $sql .= implode(',', $update);
        $sql .= ", updated_at = NOW() WHERE id = ?";
        $params = [
            $id
        ];
        $stmt = $this->executeStatement($sql, $params);
        if($stmt->affected_rows == 1) {
            $response = [
                'status' => 'Ok',
                'message' => 'record updated successfully'
            ];
        }else{
            $response = [
                'status' => 'error',
                'message' => 'can not update the row'
            ];
        }
        echo json_encode($response);
    }

    /**
     * Delete data using the DELETE method
     */
    public function deleteQuotes($id) {
        $id = $this->sanitizeInput($id['id']);
        $user = new UserController();
        $access = $user->hasAccess($id);
        if(!$access) {
            $response = [
                'status' => '403 forbidden',
                'message' => 'you dont have access to this record'
            ];
            http_response_code(403);
            echo json_encode($response);
            die();
        }
        $sql = "DELETE FROM $this->table WHERE id =?";
        $params = [
            $id
        ];
        $stmt = $this->executeStatement($sql, $params);
        if($stmt->affected_rows == 1) {
            $response = [
                'status' => 'Ok',
                'message' => 'record deleted successfully'
            ];
        }else{
            $response = [
                'status' => 'error',
                'message' => 'can not delete the record'
            ];
        }
        echo json_encode($response);
    }

    /**
     * It takes an author as input and retrieves that author's citations from the database.
     *
     * @param   [type]  $author  Name of the author
     */
    public function QuotesByAuthor($author) {
        $author = $this->sanitizeInput($author['author']);
        $author = str_replace('%20', ' ', $author);
        $sql = "SELECT * FROM $this->table WHERE author = ?";
        $params = [
            $author
        ];
        $stmt = $this->executeStatement($sql, $params);
        $rows = $stmt->get_result();
        if($rows->num_rows <= 0) {
            $response = [
                "status" => "error",
                "message" => "no record exists in database"
            ];
        }else{
            $data = [];
            while($row = $rows->fetch_assoc()) {
                $response[] = $row;
            }
        }
        echo json_encode($response);
    }

    /**
     * This function is used to retrieve quotes for a specific user based on the provided user ID.
     *
     * @param   [type]  $id  User ID to receive quotes
     */
    public function getQuoteByUserId($id) {
        $id = $this->sanitizeInput($id['id']);
        $sql = "SELECT quotes.*, users.name AS username FROM $this->table INNER JOIN users ON users.id = quotes.user_id WHERE users.id = ?";
        $params = [
            $id
        ];
        $stmt = $this->executeStatement($sql, $params);
        $rows = $stmt->get_result();
        if($rows->num_rows <= 0) {
            $response = [
                "status" => "error",
                "message" => "no record exists in database"
            ];
        }else{
            $response = [];
            while($row = $rows->fetch_assoc()) {
                $response[] = $row;
            }
        }
        echo json_encode($response);
    }

    /**
     * This function is used to get a specified number of random quotes from the data table.
     *
     * @param   [type]  $limit  Its value is determined by the user. This value is then used to limit
     *                  the number of SQL query results to return the specified number of records.
     */
    public function getQuoteByLimmit($limit) {
        $limit = $limit['limit'];
        $sql = "SELECT * FROM $this->table ORDER BY RAND() LIMIT ?";
        $params = [
            $limit
        ];
        $stmt = $this->executeStatement($sql, $params);
        $result = $stmt->get_result();
        $response = [];
        while($row = $result->fetch_assoc()) {
            $response[] = $row;
        }
        echo json_encode($response);
    }
}