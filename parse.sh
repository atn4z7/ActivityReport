#!/bin/bash

#parsing data from userinfo.txt
time=`grep "2014" userinfo.txt`
maxHR=`grep "Max:" userinfo.txt | grep -o "[0-9].*[0-9]"`
AHR=`grep "Average:" userinfo.txt | grep -o "[0-9].*[0-9]"` 
CA=`grep "Calories:" userinfo.txt | grep -o "[0-9].*[0-9]"`
IFS=$'\n' timearr=($time)
maxHRarr=($maxHR)
AHRarr=($AHR)
CAarr=($CA)
output="['Time', 'Max Heart Rate', 'Average Heart Rate', 'Total Calories'], "

#writing out arrays of data
for (( i = 0 ; i < ${#maxHRarr[@]} ; i++ )) do

	output=$output"[ \""${timearr[$i]}"\", "${maxHRarr[$i]}", "${AHRarr[$i]}", "${CAarr[$i]}
	if [ $i -eq $((n = ${#maxHRarr[@]} -1)) ] 
	then
    		output+=" ]"
	else
    		output+="], "
	fi
	echo "$output"

done

#updating report files with new data
OLD="replace"
sed "s/$OLD/$output/g" reportLayout.html > report1.html
OLD="NAME"
output=`cat name.txt`
sed "s/$OLD/$output/g" report1.html > report.html

