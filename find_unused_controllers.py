import os
import re

# Read controllers list
with open('all_controllers.txt', 'r') as f:
    controllers = [line.strip() for line in f.readlines()]

# Let's see if any controllers have odd names, "test", "old", "backup", etc.
for cont in controllers:
    name = os.path.basename(cont).lower()
    if 'test' in name or 'old' in name or 'backup' in name or 'copy' in name:
        print(f"Suspicious controller: {cont}")

