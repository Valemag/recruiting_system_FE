<?php


class FileSystem{

    protected $fileSystemUrl = "/xampp/htdocs/backEnd/fileSystem/";
    protected $uploadsPath = "files/";
    protected $utenteFolderPlaceholder = "u_";
    protected $aziendaFolderPlaceholder = "a_";
    protected $validFileExtensions = ['jpg', 'png', 'jpeg', 'pdf'];
    protected $validImageExtensions = ['jpg', 'png', 'jpeg'];


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
