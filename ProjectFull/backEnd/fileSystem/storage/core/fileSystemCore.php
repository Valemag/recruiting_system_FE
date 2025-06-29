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

    function isFileExtensionValid($fileName): bool {
        foreach ($this->validFileExtensions as $ext) {
            if (str_ends_with($fileName, $ext)) {
                return true;
            }
        }
        return false;
    }

    function isImageExtensionValid($imageName): bool {
        foreach ($this->validImageExtensions as $ext) {
            if (str_ends_with($imageName, $ext)) {
                return true;
            }
        }
        return false;
    }

}
?>
