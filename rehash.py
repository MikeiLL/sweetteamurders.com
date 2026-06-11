"""
Generate a bunch of 64 char alphanumeric strings to a file.
"""
import random, string

def rw():
   letters = string.ascii_letters+string.digits
   return ''.join(random.choice(letters) for i in range(64))

with open('hashes', 'w') as f:
   for i in range(30):
      f.write(f"{rw()}\n")
