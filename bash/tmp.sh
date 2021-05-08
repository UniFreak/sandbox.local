#!/usr/bin
declare -n var; 

for var in DISPLAY HOME LOGNAME MAIL OLDPWD PATH PWD SHELL TERM USER; do 
    echo -n ${!var}: 
    echo ${var}; 
done