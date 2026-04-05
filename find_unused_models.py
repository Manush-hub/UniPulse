import os
import re

models_dir = 'app/models'
search_dirs = ['app/controllers', 'app/models', 'app/Core', 'app/views', 'public']

# Get all model files
all_models = []
for root, dirs, files in os.walk(models_dir):
    for f in files:
        if f.endswith('.php'):
            all_models.append(os.path.join(root, f))

# Read all code
code_contents = []
for d in search_dirs:
    for root, dirs, files in os.walk(d):
        for f in files:
            if f.endswith('.php') or f.endswith('.js'):
                file_path = os.path.join(root, f)
                with open(file_path, 'r', encoding='utf-8', errors='ignore') as file:
                    code_contents.append((file_path, file.read()))

print(f"Total models found: {len(all_models)}")
print("Checking for unreferenced models...")

unreferenced_models = []

for model_path in all_models:
    base_name = os.path.basename(model_path)
    model_name = base_name.replace('.php', '')
    
    is_referenced = False
    
    # Models might be referenced as $this->model('ModelName') or new ModelName()
    # or ModelName::method()
    for cf, content in code_contents:
        if cf == model_path:
            continue
        
        # Check patterns
        if f"model('{model_name}'" in content or f'model("{model_name}"' in content:
            is_referenced = True
            break
        if f"new {model_name}" in content:
            is_referenced = True
            break
        if f"{model_name}::" in content:
            is_referenced = True
            break
        # Sometimes class name is used in type hinting:
        if f" {model_name} $" in content:
            is_referenced = True
            break
            
    if not is_referenced:
        unreferenced_models.append(model_path)

for um in unreferenced_models:
    print(f"POSSIBLY UNREFERENCED MODEL: {um}")

