<?php
interface client
{
    public function setAllFilesCount();
    public function IgnoreFile();
    public function uploadDone($spent,$avg);
    public function uploadFailed();
    public function totalFiles($ctr);
    public function starting();
    public function uploadingFile($filepath);
    public function alreadyUploaded($ignored,$total_files);
    public function setFreeSpace($space);
    public function resizingFromTo($from,$to,$saved);
    public function setDestinationDirectory($dirname);
}
