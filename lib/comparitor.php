<?php
# (c) Kevin Sandom 2020 - GPLv3. See LICENSE for details.

class Comparitor
{
    private $inputTimeStamps;
    private $editTimeStamps;
    private $outputTimeStamps;
    
    private $editTimeLine;
    
    private $changes;
    
    public function __construct($input, $edit)
    {
        $this->inputTimeStamps=$input->getTimeStamps();
        $this->editTimeStamps=$edit->getTimeStamps();
        $this->outputTimeStamps=$this->inputTimeStamps;
        
        $this->editTimeLine=$edit;
        
        $this->changes=array();
        
        if (count($this->inputTimeStamps) != count($this->editTimeStamps))
        {
            die("The number of timestamps in the original file vs the edited file are not the same. Can not continue. Maybe you have added or removed something?\n");
        }
    }
    
    public function doBasicCompare()
    {
        $this->findChanges();
        $this->assertEnoughChanges();
        $this->deriveChanges();
        $this->displayChanges();
        $this->applyOffsets();
    }
    
    public function write($filename)
    {
        $outLines=array();
        $lineNumber=0;
        
        foreach ($this->editTimeLine->getLines() as $line) // Using the edit as the source so comments can be updated if desired. But still, no new lines can be added or removed.
        {
            $lineNumber++;
            $key=$this->editTimeLine->getKeyForLineNumber($lineNumber);
            if (isTimeStampLine($line))
            {
                $outLines[]="{$this->outputTimeStamps[$key]['begin']['stamp']} --> {$this->outputTimeStamps[$key]['end']['stamp']}";
            }
            else
            {
                $outLines[]="$line";
            }
        }
        
        $result=implode("\n", $outLines);
        
        file_put_contents($filename, $result);
    }
    
    private function findChanges()
    {
        $keys=array_keys($this->inputTimeStamps);
        
        foreach ($keys as $key)
        {
            if ($this->inputTimeStamps[$key]['begin']['seconds'] != $this->editTimeStamps[$key]['begin']['seconds'])
            {
                $this->changes[$key]=$this->editTimeStamps[$key]['begin']['seconds']-$this->inputTimeStamps[$key]['begin']['seconds'];
            }
        }
    }
    
    public function displayChanges()
    {
        echo "Requested changes:\n";
        foreach ($this->changes as $key=>$details)
        {
            $newStamp=secondsToTimestamp($this->editTimeStamps[$key]['begin']['seconds']+$this->changes[$key]);
            echo "  {$this->inputTimeStamps[$key]['lineNumber']}: {$this->inputTimeStamps[$key]['begin']['stamp']} to $newStamp ({$this->changes[$key]})\n";
        }
        
        echo "Calculated offsets (by line):\n";
        foreach ($this->outputTimeStamps as $key=>$timeStamp)
        {
            $roundedOffset=round($timeStamp['offset'], 3);
            echo "  {$timeStamp['lineNumber']}:$roundedOffset";
        }
        echo "\n";
    }
    
    private function assertEnoughChanges()
    {
        echo "Assumptions:\n";
        $change_keys=array_keys($this->changes);
        $source_keys=array_keys($this->inputTimeStamps);
        $firstKey=$source_keys[0];
        $lastKey=$source_keys[count($source_keys)-1];
        $lastChangeKey=count($change_keys)-1;
        
        switch (count($this->changes))
        {
            case 0:
                displayHelp_easier();
                die("\nNo changes to the editThis file?\n");
                break;
            case 1:
                if ($change_keys[0] != $firstKey)
                {
                    echo "  Only one change, and it is not the first timestamp. So assuming 0 as a starting offset.\n";
                    $this->changes[0]=0;
                }
                
                if ($change_keys[0] != $lastKey)
                {
                    echo "  Only one change, and it is not the last timestamp. So assuming 0 as a ending ($lastKey) offset.\n";
                    $this->changes[$lastKey]=0;
                }
                break;
        }
        
        # Figure out if we need to make assumptions about the early values.
        if ($change_keys[0]!=$firstKey)
        {
            echo "  The first change is not at the beginning. Using the first change all the way until the beginning.\n";
            $this->changes[0]=$this->changes[$change_keys[0]];
        }
        
        if ($change_keys[$lastChangeKey]!=$lastKey)
        {
            echo "  The last change is not at the end. Using the first change all the way until the end.\n";
            $this->changes[$lastKey]=$this->changes[$change_keys[$lastChangeKey]];
        }
        
        ksort($this->changes);
    }
    
    private function deriveChanges()
    {
        $change_keys=array_keys($this->changes);
        
        foreach ($change_keys as $key_key=>$change_key)
        {
            $key_keyA=$change_keys[$key_key];
            
            if (isset($change_keys[$key_key+1]))
            {
                $key_keyB=$change_keys[$key_key+1];
                
                $begin=$this->inputTimeStamps[$key_keyA];
                $end=$this->inputTimeStamps[$key_keyB];
                $beginOffset=$this->changes[$key_keyA];
                $endOffset=$this->changes[$key_keyB];
                
                echo "(\$i=$key_keyA;\$i<=$key_keyB;\$i++)\n";
                for ($i=$key_keyA;$i<=$key_keyB;$i++)
                {
                    $current=$this->inputTimeStamps[$i];
                    
                    $offset=$this->calculateOffset($begin, $beginOffset, $current, $end, $endOffset);
                    $this->outputTimeStamps[$i]['offset']=$offset;
                }
            }
        }
    }
    
    private function calculateOffset($begin, $beginOffset, $current, $end, $endOffset)
    {
        $secondsDifference=$end['begin']['seconds']-$begin['begin']['seconds'];
        $progress=$current['begin']['seconds']-$begin['begin']['seconds'];
        $offsetDifference=$endOffset-$beginOffset;
        
        $offset=$beginOffset+($progress/$secondsDifference*$offsetDifference);
        
        return $offset;
    }
    
    private function applyOffsets()
    {
        foreach ($this->inputTimeStamps as $key=>$inputTimeStamp)
        {
            $this->outputTimeStamps[$key]['begin']['seconds']=$this->inputTimeStamps[$key]['begin']['seconds']+$this->outputTimeStamps[$key]['offset'];
            $this->outputTimeStamps[$key]['end']['seconds']=$this->inputTimeStamps[$key]['end']['seconds']+$this->outputTimeStamps[$key]['offset'];
            
            $this->outputTimeStamps[$key]['begin']['stamp']=secondsToTimestamp($this->outputTimeStamps[$key]['begin']['seconds']);
            $this->outputTimeStamps[$key]['end']['stamp']=secondsToTimestamp($this->outputTimeStamps[$key]['end']['seconds']);
        }
    }
}

?>
