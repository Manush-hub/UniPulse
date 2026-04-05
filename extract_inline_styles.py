import re

def process_file(file_path, output_css_path):
    with open(file_path, 'r') as f:
        content = f.read()

    # Find internal CSS <style> ... </style>
    style_blocks = re.findall(r'<style>(.*?)</style>', content, re.DOTALL | re.IGNORECASE)
    
    if style_blocks:
        with open(output_css_path, 'a') as f:
            for block in style_blocks:
                f.write("\n/* Extracted from " + file_path + " */\n")
                f.write(block)
                f.write("\n")
                
        # Remove them from file
        content = re.sub(r'<style>.*?</style>', '', content, flags=re.DOTALL | re.IGNORECASE)
        with open(file_path, 'w') as f:
            f.write(content)
        print(f"Extracted block styles from {file_path}")
    else:
        print(f"No block styles found in {file_path}")

process_file('app/views/events.view.php', 'public/assets/css/events-style.css')
process_file('app/views/eventview.view.php', 'public/assets/css/eventview-style.css')

