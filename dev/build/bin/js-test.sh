#!/bin/bash
rm -r $2
mkdir $2
java -jar JsTestDriver.jar --config $1 --port 9876 --browser /usr/bin/firefox --tests all --testOutput $2 --runnerMode DEBUG