<?php

require("core/fileSystemCore.php");

class StorageUtenti extends FileSystem{

    function createUtenteFolder($idUtente){
        global $uploadsPath, $utenteFolderPlaceholder;

        $userFolder = $this->fileSystemUrl.$this->uploadsPath.$this->utenteFolderPlaceholder.$idUtente;

        mkdir($userFolder, 0777, true);

    }

    function uploadUtenteFile($idUtente, $file){
       
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));


        if (!in_array($fileExtension, $this->validFileExtensions)) {
            return 1;
        }

        // Cartella di destinazione
        $dest_path = $this->fileSystemUrl.$this->uploadsPath.$this->utenteFolderPlaceholder.$idUtente."/". $fileName;

        // Crea la cartella se non esiste
        if (!is_dir($this->fileSystemUrl.$this->uploadsPath.$this->utenteFolderPlaceholder.$idUtente)) {
            
            return 2;

        }

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            return 0;
        } else {
            return 3;
        }
    }



}



?>