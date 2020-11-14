<?php
# (c) Kevin Sandom 2020 - GPLv3. See LICENSE for details.

function displayHelp_easier()
{
    echo "stt - SubTitle Transpose
    (c) Kevin Sandom 2020 - GPLv3. See LICENSE for details.
    Transposes subtitles when the subtitle timings don't match the video. There are many reasons why this can happen, and this will only cater to some of them. YMMV.
    
    Method
    * It takes the original subAnything outside the title file (eg filename.srt), makes a copy (eg filename-editThis.srt).
    * You correct a timestamp at the beginning, and end of the file.
    * It compares the original with the copy and 
        * makes assumptions about what's in between.
        * Anything outside the corrected timestamps will be offset by the closest correction.
    * It produces a new file (eg filename-useThis.srt) that you can use with your video.
    
    Syntax
        stt inputSubtitleFile.srt
    
    Example flow
        # Take input.srt and make the copy.
        stt input.srt
        
        # You now edit input-editThis.srt. For example, the starting timestamp is 2 seconds off. And the ending timestamp is 1 second off.
          # So you change the starting timestamp from 00:00:01,401 to 00:00:03,401.
          # And you change the ending timestamp from 01:44:40,611 to 01:44:39,611.
        
        # Run stt again figure stuff out to generate input-useThis.srt.
        stt input.srt
        
        # Hrmmmm, that's still not quite right. You corrected the ending timestamp the wrong way. So you edit input-editThis.srt again.
          # You change the ending timestamp from 00:44:39,611 to 00:44:41,611.
        
        # Now you run it again to get the final result.
        stt input.srt
        
        # You can tweak, and re-run it as many times as you like until you get it right.\n";
}

function doEasierWorkflow($argv)
{
    $filename=$argv[1];
    
    if (!file_exists($filename))
    {
        die("Sorry, I couldn't find \"$filename\"\n");
    }
    
    $filename_parts=explode('.', $filename);
    $filename_parts_truncated=$filename_parts;
    unset ($filename_parts_truncated[count($filename_parts_truncated)-1]);
    $filename_noExtention=implode('.', $filename_parts_truncated);
    
    $filename_editThis=$filename_noExtention.'-editThis.srt';
    $filename_useThis=$filename_noExtention.'-useThis.srt';
    
    if (!file_exists($filename_editThis))
    {
        $contents=file_get_contents($filename);
        if (file_put_contents($filename_editThis, $contents))
        {
            echo "Yay! Now edit at least one timestamp in $filename_editThis,\nand then run '{$argv[0]} \"$filename\"' again.\n";
        }
        else
        {
            die("Hmmmm, something didn't go right there. Please fix that, and then try again.\n");
        }
    }
    else
    {
        $inputFile=new TimeLine($filename);
        $editFile=new TimeLine($filename_editThis);
        
        $comparitor=new Comparitor($inputFile, $editFile);
        $comparitor->doBasicCompare();
        
        $comparitor->write($filename_useThis);
    }
}



?>
