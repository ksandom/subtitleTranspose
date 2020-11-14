# subtitleTranspose

A really, _really_ easy way to get subtitle timings to correctly match the video you are watching. It assumes you have a subtlte file (`.srt`) for the media you are watching.

## Workflow

1. You run `stt subtitles.srt`, where `subtitles.srt` is the name of your subtitles file in this example.
    * It takes the original subtitle file, `subtitles.srt`, and makes a copy with `-editThis` appended to the end, ie `subtitles-editThis.srt`.
1. You then edit `subtitles-editThis.srt`.
    * Start by correcting a timestamp near the beginning and end.
    * Come back and make corrections after testing what you have.
1. Now run `stt subtitles.srt` again.
    * It will look at your corrects and guess what should be in between, and before and after.

If you strike any places that are still not quite right, correct the individual timestamps one at a time, and re-run `stt subtitles.srt` after every change. On the worst-sync'd movie we've watched, it took about 6 edits to get a really good result. On most material, it only takes the start and end changes mentioned in the second step.

Note that only edits to the start timestamp of each subtitle is honoured.

## Old workflow

If you liked the old workflow, or have a particular usecase (eg you want to automate a whole heap of material), you can `legacyStt`. You can read about that [here](docs/legacyStt.md).

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
stt inputSubtitleFile
```
