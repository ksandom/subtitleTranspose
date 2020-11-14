# subtitleTranspose

Transposes subtitles when the subtitle timings don't match the video. There are many reasons why this can happen, and this will only cater to some of them. YMMV.

It takes the latest subtitle entry, and assumes it's the end of the video. Entering the videoLength gives it enough information to reposition everything between the start to the the end to be until the videoLength value instead.

However there may be some time after the last subtitle before the video ends, while the credits roll. You can specify this with creditsLengthInSeconds. It's assumed to be 33 seconds if it's not specified. Why 33? Because that's what was convenient when I wrote this.

**This is the documentation for the legacy workflow. It's still here in case it suits your situation better. But for most people, I recommend using the new workflow documented in the main README.md.**

## Install

```
sudo make install
```

## Uninstall

```
sudo make uninstall
```

## Syntax
```
legacyStt inputSubtitleFile videoLength [creditsLengthInSeconds] > outputFileName.srt
```

## Examples

Take input.srt, specify that it ends at 00:46:13, and create output.srt as the result.
```
legacyStt input.srt 00:46:13 > output.srt
```

Same, but using the .SRT timestamp format.
```
legacyStt input.srt 00:46:13,00 > output.srt
```

Same, but specify that there are 60 seconds of credits after the last subtitles.
```
legacyStt input.srt 00:46:13 60 > output.srt
```
