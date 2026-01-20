<?php 
// if (!isset($_SERVER['DOCUMENT_ROOT']) || $_SERVER['DOCUMENT_ROOT'] == '') {
//     $_SERVER['DOCUMENT_ROOT'] = dirname(__DIR__); 
// }
// include $_SERVER['DOCUMENT_ROOT'] . '/include/db_connect.php';
// include $_SERVER['DOCUMENT_ROOT'] . '/include/functions.php';
// include $_SERVER['DOCUMENT_ROOT'] . '/include/datatable.php';
// include $_SERVER['DOCUMENT_ROOT'] . '/include/FileUploader.php';

class FileUploader{
    private string $upload_path;
    private array $allowed_extensions;
    private array $allowed_mime_types;
    private int $max_size; 

    public function __construct(string $upload_path, array $allowed_extensions, array $allowed_mime_types, int $max_size){
        $this->upload_path = $upload_path;
        $this->allowed_extensions = $allowed_extensions;
        $this->allowed_mime_types = $allowed_mime_types;
        $this->max_size = $max_size;
        if(!is_dir($this->upload_path)){
            mkdir($this->upload_path , 0777, true);
        }
    }
    public function upload(string $input_name): ?string {
        if(!isset($_FILES[$input_name]) || empty($_FILES[$input_name]['name'])){
            return null;
        }
        $file=$_FILES[$input_name];
         
        if($file['error']!==0){
            throw new Exception('File Upload Error');
        }
        if ($file['size'] > $this->max_size) {
            throw new Exception('File size exceeded');
        }
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $mimeType  = mime_content_type($file['tmp_name']);

        if (!in_array($extension, $this->allowed_extensions)) {
            throw new Exception('Invalid file extension');
        }

        if (!in_array($mimeType, $this->allowed_mime_types)) {
            throw new Exception('Invalid file type');
        }

        $newFileName = uniqid('', true) . '.' . $extension;
        $destination = $this->upload_path . $newFileName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new Exception('Failed to save file');
        }

        return $newFileName;
    }

}



?>