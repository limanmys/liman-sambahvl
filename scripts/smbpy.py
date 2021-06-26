#!/usr/bin/python3

import subprocess, os

servicecmd = "systemctl status samba4.service"
service = subprocess.Popen(servicecmd,shell=True,stdout=subprocess.PIPE,stderr=subprocess.STDOUT)
service = service.communicate()[0]
service = service.decode("utf-8").rstrip("\n")

print(service)



