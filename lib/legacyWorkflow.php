<?php
# (c) Kevin Sandom 2020 - GPLv3. See LICENSE for details.

function displayHelp_legacy()
{
    echo "legacyStt - SubTitle Transpose
    (c) Kevin Sandom 2020 - GPLv3. See LICENSE for details.
    Transposes subtitles when the subtitle timings don't match the video. There are many reasons why this can happen, and this will only cater to some of them. YMMV.
    
    It takes the latest subtitle entry, and assumes it's the end of the video. Entering the videoLength gives it enough information to reposition everything between the start to the the end to be until the videoLength value instead.
    
    However there may be some time after the last subtitle before the video ends, while the credits roll. You can specify this with creditsLengthInSeconds. It's assumed to be 33 seconds if it's not specified. Why 33? Because that's what was convenient when I wrote this.
    
    Syntax
        legacyStt inputSubtitleFile videoLength [creditsLengthInSeconds] > outputFileName.srt
    
    Examples
        # Take input.srt, specify that it ends at 00:46:13, and create output.srt as the result.
        legacyStt input.srt 00:46:13 > output.srt
        
        # Same, but using the .SRT timestamp format.
        legacyStt input.srt 00:46:13,00 > output.srt
        
        # Same, but specify that there are 60 seconds of credits after the last subtitles.
        legacyStt input.srt 00:46:13 60 > output.srt\n";
}

function doLegacyWorkflow($argv)
{
    $file_in=$argv[1];
    $destinationTime=$argv[2];
    $file_out='ql-out.srt';

    $contents = file_get_contents($file_in);
    $lines=explode("\n", $contents);
    $output=array();


    # Destination time
    $creditsTime=(isset($argv[3]))?$argv[3]:33; # Seconds



    # Find out the largest time that we have.
    $largestFromTime=0;
    foreach ($lines as $line)
    {
        if ($result=strpos($line, '-->') !== false)
        {
            $times=timestampsToTimes(lineToTimestamps($line));
            if ($times[1]>$largestFromTime) $largestFromTime=$times[1];
        }
    }

    # Convert the destinationTime to something we can use.
    $toTime=timeStampToSeconds($destinationTime)-$creditsTime;


    foreach ($lines as $line)
    {
        if ($result=strpos($line, '-->') !== false)
        {
            $times=timestampsToTimes(lineToTimestamps($line));
            $times[0]=transposeTime($times[0], $largestFromTime, $toTime);
            $times[1]=transposeTime($times[1], $largestFromTime, $toTime);
            
            $timestamps=timesToTimestamps($times);
            
            echo "{$timestamps[0]} --> {$timestamps[1]}\n";
        }
        else
        {
            echo "$line\n";
        }
    }
}


?>
