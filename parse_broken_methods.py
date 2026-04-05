import re

files_to_fix = {
    'app/controllers/Userprofile.php': ['userprofile'],
    'app/controllers/Userpublic.php': ['userpublic'],
    'app/controllers/Volunteerreg.php': ['volunteerreg'],
    'app/controllers/Onboarding.php': ['role-selection', 'multi-step-registration'],
    'app/controllers/Admin.php': ['Admin/users'],
    'app/controllers/Publisher/Sponsorships.php': ['Publisher/sponsorship-view'],
    'app/controllers/Moderator/Userreports.php': ['Moderator/user_reports'],
    'app/controllers/Moderator/Contentmoderation.php': ['Moderator/content_moderation'],
    'app/controllers/Sponsor/Sponsorships.php': ['Sponsor/sponsorship-view'],
    'app/controllers/Sponsor/Sponsorship.php': ['Sponsor/sponsorship-view']
}

print("Checking which methods to delete:")

for file, missing_views in files_to_fix.items():
    with open(file, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # We will regex to find the method that contains the string
    # A method starts with `public function name` or `protected function name` or `function name`
    
    methods = re.split(r'(?:public|protected|private)?\s*function\s+', content)
    
    for i, method_body in enumerate(methods[1:]):  # skip class header
        method_name = method_body.split('(')[0].strip()
        
        for mv in missing_views:
            if f"'{mv}'" in method_body or f'"{mv}"' in method_body:
                print(f"{file} -> method: {method_name} calls missing view {mv}")

