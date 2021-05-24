import os
import sys

configString = sys.argv[1]
configPath = sys.argv[2]
configList = list()

configList = configString.split('\n')

with open(configPath,'w') as configFile:
    for i in range(len(configList)):
        configFile.write(configList[i] + '\n')

configFile.close()