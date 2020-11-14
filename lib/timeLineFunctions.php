<?php
# (c) Kevin Sandom 2020 - GPLv3. See LICENSE for details.

function timeStampToSeconds($timeStamp)
{
    $parts=explode(':', $timeStamp);
    
    $parts[2]=implode('.', explode(',', $parts[2]));
    
    $resultParts=array();
    $resultParts[0]=$parts[0]*3600;
    $resultParts[1]=$parts[1]*60;
    $resultParts[2]=trim($parts[2]);
    
    $result=$resultParts[0]+$resultParts[1]+$resultParts[2];
    return $result;
}

function secondsToTimestamp($seconds)
{
    $hours=intval($seconds/3600);
    $seconds=$seconds-($hours*3600);
    $hoursPad=str_pad($hours, 2, '0', STR_PAD_LEFT);
    
    $minutes=intval($seconds/60);
    $seconds=round($seconds-($minutes*60), 3);
    $minutesPad=str_pad($minutes, 2, '0', STR_PAD_LEFT);
    
    if (strpos($seconds, '.'))
    {
        $secondsParts=explode('.', $seconds);
    }
    else
    {
        $secondsParts=explode(',', $seconds);
    }
    
    if (!isset($secondsParts[1])) $secondsParts[1]='000';
    
    $secondsParts[0]=str_pad($secondsParts[0], 2, '0', STR_PAD_LEFT);
    $secondsParts[1]=str_pad($secondsParts[1], 3, '0', STR_PAD_RIGHT);
    $secondsPad=implode(',', $secondsParts);
    
    $result="$hoursPad:$minutesPad:$secondsPad";
    return $result;
}

function lineToTimestamps($line)
{
    return explode(' --> ', $line);
}

function timestampsToTimes($timestamps)
{
    foreach ($timestamps as $key=>&$timeStamp)
    {
        $timeStamp=timeStampToSeconds($timeStamp);
    }
    
    return $timestamps;
}

function timesToTimestamps($times)
{
    foreach ($times as $key=>&$time)
    {
        $time=secondsToTimestamp($time);
    }
    
    return $times;
}

function transposeTime($inputTime, $fromEnd, $toEnd)
{
    return $inputTime/$fromEnd*$toEnd;
}

function isTimeStampLine($line)
{
    return (strpos($line, '-->'));
}

?>
