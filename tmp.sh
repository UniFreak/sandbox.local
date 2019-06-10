#!/bin/sh
dummy() {
    local i # local variable
    echo "$1" # use parameter
    return 9 # return code
}

# call
dummy one
# call with parameter
r=dumm
t=$(dummy "two")
echo "$r" "$t" "$?"