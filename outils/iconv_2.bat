echo off
echo "%1"
iconv.exe -f LATIN1 -t UTF-8 "%1">"%1_"

