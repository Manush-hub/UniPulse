import os
import re

view_dir = 'app/views'
controllers_dir = 'app/controllers'

# Gather all .php files in app/views
all_views = []
for root, dirs, files in os.walk(view_dir):
    for f in files:
        if f.endswith('.php'):
            all_views.append(os.path.join(root, f))

# Gather all code files
code_files = []
for d in ['app/controllers', 'app/Core', 'app/views']:
    for root, dirs, files in os.walk(d):
        for f in files:
            if f.endswith('.php'):
                code_files.append(os.path.join(root, f))

# Read all code content
code_contents = []
for cf in code_files:
    with open(cf, 'r', encoding='utf-8', errors='ignore') as f:
        code_contents.append((cf, f.read()))

print(f"Total views found: {len(all_views)}")
print("Checking for unreferenced views...")

unreferenced_views = []

for view_path in all_views:
    rel_path = os.path.relpath(view_path, view_dir)
    # Different ways the view might be referenced:
    # 1. Without .view.php or .php
    base_name = os.path.basename(view_path)
    name_no_ext = base_name.replace('.view.php', '').replace('.php', '')
    
    # 2. Path style e.g. "Admin/dashboard"
    path_no_ext = rel_path.replace('.view.php', '').replace('.php', '').replace('\\', '/')
    
    is_referenced = False
    
    # Look for these strings in all code contents
    for cf, content in code_contents:
        if cf == view_path:
            continue # Don't count self-references if any (well, might be possible but let's ignore)
        
        # Check if the exact path without extension is in the file
        if f"'{path_no_ext}'" in content or f'"{path_no_ext}"' in content:
            is_referenced = True
            break
        # Check if just the basename is used in a view() call
        if f"view('{name_no_ext}'" in content or f'view("{name_no_ext}"' in content:
            is_referenced = True
            break
        # Check if it's included/required by its filename
        if f"'{base_name}'" in content or f'"{base_name}"' in content:
            is_referenced = True
            break
        # Or just checking if name_no_ext appears in the file if it's a very unique name
        # But we'll be safe and check specific patterns
        if re.search(rf"(?:include|require).*?['\"].*?{name_no_ext}(?:\.view)?\.php['\"]", content):
            is_referenced = True
            break
            
    if not is_referenced:
        unreferenced_views.append(view_path)

for uv in unreferenced_views:
    print(f"UNREFERENCED: {uv}")

