
import sys
import wordninja

domain = sys.argv[1]
name = domain.split('.')[0]
words = wordninja.split(name)
print(" ".join(words))

