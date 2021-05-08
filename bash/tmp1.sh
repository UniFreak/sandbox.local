#!/bin/sh
echo -n "Enter name: "
read name

# 2. 使用 -p 指定提示符及变量
read -p "Enter age, year: " age year

# 3. 不指定变量, 则自动保存在 REPLY 中
read -p "Enter anything: "

echo $name, $age, $year, $REPLY

if read -t 3 -p "Counting down..." input; then
    echo "Get input $input"
else
    echo
    echo "Too slow...: $input"
fi