<?php
class ClientConsole implements client
{
	public function setAllFilesCount()
	{

	}
	public function IgnoreFile()
	{

	}

	public function uploadDone($spent,$avg)
	{
        echo " upload $spent s ($avg s/file)\n";

	}

	public function uploadFailed()
	{
        echo "Failed.\n";
	}

	public function totalFiles($ctr)
	{
	    echo "found . " . $ctr;
	}
	public function starting()
	{
		echo "starting...";
	}

	public function uploadingFile($filepath)
	{
		$filepath_strip = substr($filepath,-60);
        echo "#uploading $filepath_strip...";
	}

	public function alreadyUploaded($ignored,$total_files)
	{
		$pc = round(100*($ignored/$total_files));
		echo ",#file already uploaded ($ignored/$total_files $pc% ignored)\n";
	}

	public function setFreeSpace($space)
	{
		echo "# (" . round($space/(1024*1024)) . " MB free space) \n";
	}

	public function resizingFromTo($from,$to,$saved)
	{
		echo "#resizing $from to $to, total space saved: " . round($saved/(1024*1024)) . "Mb ";
	}
	public function setDestinationDirectory($dirname)
	{
		echo "#Destination Directory :" . $dirname;
	}
}
