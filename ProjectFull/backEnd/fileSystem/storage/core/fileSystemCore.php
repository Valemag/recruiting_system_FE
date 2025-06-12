<?php


class FileSystem{

    private $fileSystemUrl = "https://127.0.0.1/bitByte/backEnd/fileSystem/";
    private $uploadsPath = "files/";
    private $utenteFolderPlaceholder = "u_";
    private $aziendaFolderPlaceholder = "a_";
    private $validFileExtensions = ['jpg', 'png', 'jpeg', 'pdf'];
    private $validImageExtensions = ['jpg', 'png', 'jpeg'];


    function getFileSystemUrl(){

        return $this -> fileSystemUrl;
    }

    function getUploadsPath(){

        return $this -> uploadsPath;
    }

    function getUtenteFolderPlaceholder(){

        return $this -> utenteFolderPlaceholder;

    }

    function getAziendaFolderPlaceholder(){

        return $this -> aziendaFolderPlaceholder;

    }

    function getValidFileExtensions(){

        return $this -> validFileExtensions;

    }

    function getValidImageExtensions(){

        return $this -> validImageExtensions;

    }

    function isImageExtensionValid($ext){

        return in_array($ext, $this -> validImageExtensions);

    }

    function isFileExtensionValid($ext){

        return in_array($ext, $this -> validFileExtensions);

    }

}
?>