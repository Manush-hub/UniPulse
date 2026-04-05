import os
import re

controllers_dir = 'app/controllers'

for root, dirs, files in os.walk(controllers_dir):
    for f in files:
        if f.endswith('.php'):
            file_path = os.path.join(root, f)
            with open(file_path, 'r', encoding='utf-8', errors='ignore') as file:
                content = file.read()
                
            # Finding all view(...) calls
            views_called = re.findall(r"(?:\$this|parent)->view\(\s*['\"]([^'\"]+)['\"]", content)
            
            for view in views_called:
                # view format can be 'Role/name' or 'name' 
                # we need to consider how Controller.php resolves it. 
                # Generally it resolves to `app/views/{Role}/{view}.view.php` or `app/views/{view}.view.php`
                
                # We'll just check if `{view}.view.php` or `{view}.php` exist anywhere in app/views
                found = False
                view_name_to_check = view.replace('.view', '').replace('.php', '')
                
                # fast check
                if os.path.exists(f'app/views/{view_name_to_check}.view.php') or os.path.exists(f'app/views/{view_name_to_check}.php'):
                    found = True
                else:
                    # check deeper
                    for vr, vd, vf in os.walk('app/views'):
                        if f'{view_name_to_check}.view.php' in vf or f'{view_name_to_check}.php' in vf or f"{os.path.basename(view_name_to_check)}.view.php" in vf or f"{os.path.basename(view_name_to_check)}.php" in vf:
                            vp = os.path.join(vr, f"{os.path.basename(view_name_to_check)}.view.php")
                            if os.path.exists(vp):
                                found = True
                            
                            vp2 = os.path.join(vr, f"{view_name_to_check}.view.php")
                            if os.path.exists(vp2) or vp2.replace('\\', '/').endswith(f"{view_name_to_check}.view.php"):
                                found = True
                                
                            vp3 = os.path.join(vr, f"{os.path.basename(view_name_to_check)}.php")
                            if os.path.exists(vp3):
                                found = True
                                
                            vp4 = os.path.join(vr, f"{view_name_to_check}.php")
                            if os.path.exists(vp4) or vp4.replace('\\', '/').endswith(f"{view_name_to_check}.php"):
                                found = True
                            
                if not found:
                    
                    # Sometimes the path is specified literally with Role like $this->view('Sponsor/browse-events')
                    # And if it was deleted, it is broken!
                    print(f"Controller broken: {file_path} calls missing view '{view}'")

