
<?php

require("core/fileSystemCore.php");

class StorageAziende extends FileSystem{

    function createAziendaFolder($idAzienda){

        $userFolder = $this->fileSystemUrl.$this->uploadsPath.$this->aziendaFolderPlaceholder.$idAzienda;

        mkdir($userFolder, 0777, true);

    }

    function uploadAziendaFile($idAzienda, $file){

        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));


        if (!in_array($fileExtension, $this->validImageExtensions)) {
            return 1;
        }

        // Cartella di destinazione
        $dest_path = $this->fileSystemUrl.$this->uploadsPath.$this->aziendaFolderPlaceholder.$idAzienda."/". $fileName;

        // Crea la cartella se non esiste
        if (!is_dir($this->fileSystemUrl.$this->uploadsPath.$this->aziendaFolderPlaceholder.$idAzienda)) {
            
            return 2;

        }

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            return 0;
        } else {
            return 3;
        }
    }

    function deleteAziendaFile($idAzienda, $fileName) {
    
        $filePath = $this->fileSystemUrl.$this->uploadsPath.$this->aziendaFolderPlaceholder . $idAzienda . "/" . $fileName;
    
        if (!file_exists($filePath)) {
            return 1; // File non trovato
        }
    
        if (unlink($filePath)) {
            return 0; // Eliminazione riuscita
        } else {
            return 2; // Errore durante l'eliminazione
        }
    }    


}



?>