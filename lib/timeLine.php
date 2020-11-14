<?php
# (c) Kevin Sandom 2020 - GPLv3. See LICENSE for details.

class TimeLine
{
    private $lines;
    private $timeStamps;
    private $offsets;
    
    private $lineMap;
    
    private $highestSeconds=null;
    private $lowestSeconds=null;
    
    public function __construct($filename)
    {
        $contents = file_get_contents($filename);
        $this->lines=explode("\n", $contents);
        $this->lineMap=array();
        
        $this->loadTimeStamps();
    }
    
    public function getTimeStamps()
    {
        return $this->timeStamps;
    }
    
    public function getLines()
    {
        return $this->lines;
    }
    
    public function getKeyForLineNumber($lineNumber)
    {
        if (isset($this->lineMap[$lineNumber])) return $this->lineMap[$lineNumber];
        else return null;
    }
    
    private function loadTimeStamps()
    {
        $this->timeStampKeys=array();
        $this->timeStamps=array();
        $this->offsets=array();
        
        $lineNumber=0;
        foreach($this->lines as $key=>$line)
        {
            $lineNumber++;
            
            if (isTimeStampLine($line))
            {
                $stamps=lineToTimestamps($line);
                
                $newItem=array(
                    'begin'=>array('stamp'=>trim($stamps[0]), 'seconds'=>timeStampToSeconds($stamps[0])),
                    'end'=>array('stamp'=>trim($stamps[1]), 'seconds'=>timeStampToSeconds($stamps[1])),
                    'offset'=>0,
                    'lineNumber'=>$lineNumber
                    );
                
                if (($newItem['begin']['seconds'] < $this->lowestSeconds) or $this->lowestSeconds==null)
                {
                    $this->lowestSeconds=$newItem['begin']['seconds'];
                }
                
                if ($newItem['end']['seconds'] > $this->highestSeconds)
                {
                    $this->highestSeconds=$newItem['end']['seconds'];
                }
                
                $this->timeStamps[]=$newItem;
            
                $this->lineMap[$lineNumber]=count($this->timeStamps)-1;
            }
        }
    }
}


?>
