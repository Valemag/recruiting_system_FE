
<?php

require("core/fileSystemCore.php");

class StorageAziende extends FileSystem{

    function createAziendaFolder($idAzienda){
        global $uploadsPath, $aziendaFolderPlaceholder;

        $userFolder = $uploadsPath.$aziendaFolderPlaceholder.$idAzienda;

        mkdir($userFolder, 0755, true);

    }

    function uploadAziendaFile($idAzienda, $file){
        global $uploadsPath, $aziendaFolderPlaceholder, $validImageExtensions;

        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));


        if (!in_array($fileExtension, $validImageExtensions)) {
            return 1;
        }

        // Cartella di destinazione
        $dest_path = $uploadsPath.$aziendaFolderPlaceholder.$idAzienda."/". $fileName;

        // Crea la cartella se non esiste
        if (!is_dir($uploadsPath.$aziendaFolderPlaceholder.$idAzienda)) {
            
            return 2;

        }

        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            return 0;
        } else {
            return 3;
        }
    }

    function deleteAziendaFile($idAzienda, $fileName) {
        global $uploadsPath, $aziendaFolderPlaceholder;
    
        $filePath = $uploadsPath . $aziendaFolderPlaceholder . $idAzienda . "/" . $fileName;
    
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