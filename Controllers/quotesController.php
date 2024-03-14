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
        $sql = "SELECT * FROM $this->table";
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
        var_dump($params);
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
        $data = $this->sanitizeInput($_POST);
        if(array_key_exists('quote', $data) || array_key_exists('author', $data)) {
            $user_id = 1;
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
}