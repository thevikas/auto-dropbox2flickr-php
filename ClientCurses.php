<?php
/**
 * @see http://devzone.zend.com/173/using-ncurses-in-php/
 */
class ClientNcurses //implements client
{
	var $fullscreen;
	var $mx = 0;
	var $my=0;
	public function __construct()
	{
		echo "Initializing Ncurses...\n";
		ncurses_init();
    	$this->fullscreen = ncurses_newwin ( 0, 0, 0, 0);
    	ncurses_border(0,0, 0,0, 0,0, 0,0);
		ncurses_getmaxyx($this->fullscreen,$this->mx,$this->my);
    	ncurses_refresh();// paint both windows
	}
	public function __destruct()
	{
		ncurses_end();
	}
	
    public function IgnoreFile()
    {

    }
    public function setDestinationDirectory($dirname)
    {
    	ncurses_refresh();// paint both windows
    	$line = "Destination: " . $dirname;
    	ncurses_move(1,$this->my - strlen($line) - 2);
    	ncurses_addstr($line);
    	ncurses_refresh();// paint both windows
    }
    
    public function setFreeSpace($space)
    {
    	$free = round($space/(1024*1024));
    	ncurses_refresh();// paint both windows
    	$line = "Space: " . $free . ' MB';
    	ncurses_move(2,$this->my - strlen($line) - 2);
    	ncurses_addstr($line);
    	ncurses_refresh();// paint both windows
   	}
   	
   	public function starting()
   	{
   		$line = "Starting...";
   		ncurses_move(1,2);
   		ncurses_addstr($line);
   		ncurses_refresh();// paint both windows
   	}
   	
   	public function totalFiles($ctr)
   	{
   		$line = "Total Files: " . $ctr;
    	ncurses_move(3,2);
    	ncurses_addstr($line);
    	ncurses_refresh();// paint both windows
   	}
   	
   	public function uploadingFile($filepath)
   	{
   		$filepath_strip = substr($filepath,- ($this->my));
   		$line = "Uploading: " . $filepath_strip;
   		ncurses_move(5,2);
   		ncurses_clrtoeol();
   		ncurses_addstr($line);
   		ncurses_refresh();// paint both windows
   	}
   	
   	public function alreadyUploaded($ignored,$total_files)
   	{
   		$pc = round(100*($ignored/$total_files));
   		#echo ",#file already uploaded ($ignored/$total_files $pc% ignored)\n";

		$line = "($ignored/$total_files $pc% ignored)";
   		ncurses_move(6,$this->my - strlen($line) - 2);
    	ncurses_addstr($line);
   		ncurses_refresh();// paint both windows
   	}
   	
}
