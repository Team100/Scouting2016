rem
rem 
rem Uploads tablet data and moves tablet files to uploaded directory
rem
rem Driven by various variables

set pscp=\apps\putty\pscp.exe -i c:\jlv\wildhats.ppk

set homedir=c:\TabletFiles

set remote=roboload@wildhats.summit8.net:/home/roboload/TabletServer

set complete=c:\TabletFiles\upload-complete

rem transfer pit files
%pscp% %homedir%\ingest-pit\* %remote%/ingest-pit

rem transfer pit files
%pscp% %homedir%\ingest-match\* %remote%/ingest-match


rem move previous transfers
delete %complete%\TabletFiles.10
rename %complete%\TabletFiles.9 %complete%\TabletFiles.10
rename %complete%\TabletFiles.8 %complete%\TabletFiles.9
rename %complete%\TabletFiles.7 %complete%\TabletFiles.8
rename %complete%\TabletFiles.6 %complete%\TabletFiles.7
rename %complete%\TabletFiles.5 %complete%\TabletFiles.6
rename %complete%\TabletFiles.4 %complete%\TabletFiles.5
rename %complete%\TabletFiles.3 %complete%\TabletFiles.4
rename %complete%\TabletFiles.2 %complete%\TabletFiles.3
rename %complete%\TabletFiles.1 %complete%\TabletFiles.2
rename %complete%\TabletFiles.0 %complete%\TabletFiles.1

rem make new directory
mkdir %complete%\TabletFiles.0
mkdir %complete%\TabletFiles.0\ingest-match
mkdir %complete%\TabletFiles.0\ingest-pit

rem rename files out of tranfer directories
move %homedir%\ingest-pit\* %complete%\TabletFiles.0\ingest-pit
move %homedir%\ingest-match\* %complete%\TabletFiles.0\ingest-match

rem batch file complete